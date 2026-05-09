<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class DetectedLogsService
{
    private function getServiceAccount(): array
    {
        $credentialsPath = config('firebase.projects.mangoguard.credentials');

        if ($credentialsPath && file_exists($credentialsPath)) {
            $json = json_decode((string) file_get_contents($credentialsPath), true);
            if (is_array($json) && isset($json['client_email'], $json['private_key'])) {
                return $json;
            }
        }

        if (
            env('FIREBASE_PROJECT_ID') &&
            env('FIREBASE_CLIENT_EMAIL') &&
            env('FIREBASE_PRIVATE_KEY')
        ) {
            $pk = trim((string) env('FIREBASE_PRIVATE_KEY'), "\"'");
            $pk = str_replace(["\\r\\n", "\\n"], "\n", $pk);

            return [
                'type' => 'service_account',
                'project_id' => env('FIREBASE_PROJECT_ID'),
                'private_key' => $pk,
                'client_email' => env('FIREBASE_CLIENT_EMAIL'),
                'token_uri' => 'https://oauth2.googleapis.com/token',
            ];
        }

        throw new \RuntimeException('Firebase credentials not configured for Firestore REST.');
    }

    private function projectId(): string
    {
        $sa = $this->getServiceAccount();
        $id = (string) ($sa['project_id'] ?? env('FIREBASE_PROJECT_ID', ''));

        if ($id === '') {
            throw new \RuntimeException('Missing FIREBASE_PROJECT_ID.');
        }

        return $id;
    }

    /**
     * List documents from Firestore collection (REST API, no gRPC).
     *
     * @return array<int, array{id: string, fields: array<string, mixed>, createTime: ?string, updateTime: ?string}>
     */
    public function fetchDetectedLogs(int $pageSize = 200): array
    {
        $collection = config('firebase.projects.mangoguard.firestore.detected_logs_collection', 'detectedLogs');
        $collection = trim((string) $collection, '/');

        $serviceAccount = $this->getServiceAccount();
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/datastore'],
            $serviceAccount
        );

        $token = $credentials->fetchAuthToken();
        $accessToken = $token['access_token'] ?? null;

        if (! $accessToken) {
            throw new \RuntimeException('Unable to obtain access token for Firestore.');
        }

        $projectId = $this->projectId();
        $parent = sprintf(
            'projects/%s/databases/(default)/documents',
            rawurlencode($projectId)
        );

        $url = sprintf(
            'https://firestore.googleapis.com/v1/%s/%s',
            $parent,
            rawurlencode($collection)
        );

        $client = new Client(['timeout' => 20]);

        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
            ],
            'query' => [
                'pageSize' => min(max($pageSize, 1), 300),
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);

        if (! is_array($body)) {
            return [];
        }

        $documents = $body['documents'] ?? [];
        $out = [];

        foreach ($documents as $doc) {
            if (! is_array($doc)) {
                continue;
            }

            $name = (string) ($doc['name'] ?? '');
            $id = $name !== '' ? basename($name) : '';

            $fields = [];
            foreach ($doc['fields'] ?? [] as $key => $wrapped) {
                if (is_array($wrapped)) {
                    $fields[$key] = $this->decodeValue($wrapped);
                }
            }

            $out[] = [
                'id' => $id,
                'fields' => $fields,
                'createTime' => $doc['createTime'] ?? null,
                'updateTime' => $doc['updateTime'] ?? null,
            ];
        }

        usort($out, function ($a, $b) {
            return strcmp((string) ($b['updateTime'] ?? $b['createTime'] ?? ''), (string) ($a['updateTime'] ?? $a['createTime'] ?? ''));
        });

        return $out;
    }

    /**
     * @param  array<string, mixed>  $wrapped
     */
    private function decodeValue(array $wrapped): mixed
    {
        if (array_key_exists('nullValue', $wrapped)) {
            return null;
        }
        if (isset($wrapped['stringValue'])) {
            return $wrapped['stringValue'];
        }
        if (isset($wrapped['integerValue'])) {
            return (int) $wrapped['integerValue'];
        }
        if (isset($wrapped['doubleValue'])) {
            return (float) $wrapped['doubleValue'];
        }
        if (isset($wrapped['booleanValue'])) {
            return (bool) $wrapped['booleanValue'];
        }
        if (isset($wrapped['timestampValue'])) {
            return $wrapped['timestampValue'];
        }
        if (isset($wrapped['mapValue']['fields']) && is_array($wrapped['mapValue']['fields'])) {
            $nested = [];
            foreach ($wrapped['mapValue']['fields'] as $k => $v) {
                $nested[$k] = is_array($v) ? $this->decodeValue($v) : null;
            }

            return $nested;
        }
        if (isset($wrapped['arrayValue']['values']) && is_array($wrapped['arrayValue']['values'])) {
            return array_map(
                fn ($v) => is_array($v) ? $this->decodeValue($v) : null,
                $wrapped['arrayValue']['values']
            );
        }

        return null;
    }

    /**
     * Read the first matching field from Firestore-decoded document fields.
     *
     * @param  array<string, mixed>  $fields
     */
    public static function pickField(array $fields, array $candidates): mixed
    {
        foreach ($candidates as $name) {
            if (array_key_exists($name, $fields)) {
                return $fields[$name];
            }
        }

        return null;
    }

    /**
     * Pick a display timestamp from decoded fields (flexible key names).
     */
    public static function guessDetectedAt(array $fields): ?string
    {
        foreach (['detectedAt', 'detected_at', 'timestamp', 'time', 'createdAt', 'created_at', 'date'] as $key) {
            if (! empty($fields[$key])) {
                return (string) $fields[$key];
            }
        }

        return null;
    }

    public function summarizeForDashboard(array $logs): array
    {
        $total = count($logs);
        $since = now()->subDay();
        $last24 = 0;

        foreach ($logs as $row) {
            $ts = self::guessDetectedAt($row['fields'] ?? []);
            if ($ts === null) {
                $ts = $row['updateTime'] ?? $row['createTime'] ?? null;
            }
            if ($ts !== null) {
                try {
                    if (\Carbon\Carbon::parse($ts)->gte($since)) {
                        $last24++;
                    }
                } catch (\Throwable) {
                    // ignore parse errors
                }
            }
        }

        return [
            'total' => $total,
            'last_24h' => $last24,
        ];
    }
}
