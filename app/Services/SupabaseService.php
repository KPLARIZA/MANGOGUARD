<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $url;
    protected $key;
    protected $serviceKey;

    public function __construct()
    {
        $this->url = config('supabase.url');
        $this->key = config('supabase.anon_key');
        $this->serviceKey = config('supabase.service_role_key');

        if (!$this->url || !$this->key) {
            throw new \RuntimeException('Supabase is not configured. Set SUPABASE_URL and SUPABASE_ANON_KEY in .env');
        }
    }

    /**
     * Authenticate user with email and password
     */
    public function authSignIn(string $email, string $password)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => "Bearer {$this->key}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->url}/auth/v1/token?grant_type=password", [
                'email' => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($this->formatHttpError('Supabase sign-in failed', $response));
        } catch (\Exception $e) {
            Log::error('Supabase auth error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sign up a new user
     */
    public function authSignUp(string $email, string $password, array $metadata = [])
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => "Bearer {$this->key}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->url}/auth/v1/signup", [
                'email' => $email,
                'password' => $password,
                'data' => $metadata,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($this->formatHttpError('Supabase sign-up failed', $response));
        } catch (\Exception $e) {
            Log::error('Supabase signup error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user by access token
     */
    public function getUser(string $accessToken)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => "Bearer {$accessToken}",
                'Accept' => 'application/json',
            ])->get("{$this->url}/auth/v1/user");

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($this->formatHttpError('Supabase get user failed', $response));
        } catch (\Exception $e) {
            Log::error('Supabase get user error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Database operations - Select from table
     */
    public function from(string $table)
    {
        return new SupabaseQueryBuilder($this->url, $this->key, $table);
    }

    /**
     * Insert into table (using service role key for admin operations)
     */
    public function insert(string $table, array $data)
    {
        try {
            if (!$this->serviceKey) {
                throw new \RuntimeException('Missing SUPABASE_SERVICE_ROLE_KEY in .env (needed for server-side inserts).');
            }

            $response = Http::withHeaders([
                'apikey' => $this->serviceKey,
                'Authorization' => "Bearer {$this->serviceKey}",
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
                'Accept' => 'application/json',
            ])->post("{$this->url}/rest/v1/{$table}", $data);

            if ($response->successful()) {
                $result = $response->json();
                // PostgREST returns array of inserted rows
                return is_array($result) && isset($result[0]) ? $result[0] : $result;
            }

            $error = $response->json();
            throw new \Exception($this->formatHttpError("Supabase insert into {$table} failed", $response));
        } catch (\Exception $e) {
            Log::error('Supabase insert error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update table (using service role key for admin operations)
     */
    public function update(string $table, string $id, array $data)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->serviceKey,
                'Authorization' => "Bearer {$this->serviceKey}",
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
            ])->patch("{$this->url}/rest/v1/{$table}?id=eq.{$id}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($response->json()['message'] ?? 'Update failed');
        } catch (\Exception $e) {
            Log::error('Supabase update error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete from table (using service role key for admin operations)
     */
    public function delete(string $table, string $id)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->serviceKey,
                'Authorization' => "Bearer {$this->serviceKey}",
            ])->delete("{$this->url}/rest/v1/{$table}?id=eq.{$id}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Supabase delete error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upsert into table (insert or update on conflict).
     * $onConflict should be a column name (e.g. "id" or "email").
     */
    public function upsert(string $table, array $data, string $onConflict = 'id')
    {
        try {
            if (!$this->serviceKey) {
                throw new \RuntimeException('Missing SUPABASE_SERVICE_ROLE_KEY in .env (needed for server-side upserts).');
            }

            $response = Http::withHeaders([
                'apikey' => $this->serviceKey,
                'Authorization' => "Bearer {$this->serviceKey}",
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation,resolution=merge-duplicates',
                'Accept' => 'application/json',
            ])->post("{$this->url}/rest/v1/{$table}?on_conflict={$onConflict}", $data);

            if ($response->successful()) {
                $result = $response->json();
                return is_array($result) && isset($result[0]) ? $result[0] : $result;
            }

            throw new \Exception($this->formatHttpError("Supabase upsert into {$table} failed", $response));
        } catch (\Exception $e) {
            Log::error('Supabase upsert error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function formatHttpError(string $prefix, $response): string
    {
        // Illuminate\Http\Client\Response
        $status = method_exists($response, 'status') ? $response->status() : null;
        $body = method_exists($response, 'body') ? trim((string) $response->body()) : '';

        $msg = '';
        try {
            $json = method_exists($response, 'json') ? $response->json() : null;
            if (is_array($json)) {
                $msg =
                    $json['error_description'] ??
                    $json['message'] ??
                    $json['msg'] ??
                    $json['error'] ??
                    '';
            }
        } catch (\Throwable $e) {
            // ignore JSON parse errors
        }

        $details = $msg ?: ($body ?: 'No response body');
        $codePart = $status ? " (HTTP {$status})" : '';

        // Avoid leaking secrets: we never include headers/keys here.
        return "{$prefix}{$codePart}: {$details}";
    }
}
