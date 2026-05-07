<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseQueryBuilder
{
    protected $url;
    protected $key;
    protected $table;
    protected $filters = [];
    protected $select = '*';
    protected $orderBy = null;
    protected $limit = null;

    public function __construct(string $url, string $key, string $table)
    {
        $this->url = $url;
        $this->key = $key;
        $this->table = $table;
    }

    public function select(string $columns)
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value)
    {
        $this->filters[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc')
    {
        $this->orderBy = "{$column}.{$direction}";
        return $this;
    }

    public function limit(int $count)
    {
        $this->limit = $count;
        return $this;
    }

    public function get()
    {
        $url = "{$this->url}/rest/v1/{$this->table}";
        $params = [];

        if ($this->select !== '*') {
            $params['select'] = $this->select;
        }

        // Build PostgREST filter queries: column=eq.value
        foreach ($this->filters as $filter) {
            $operator = $this->mapOperator($filter['operator']);
            $value = urlencode($filter['value']);
            $params["{$filter['column']}"] = "{$operator}.{$value}";
        }

        if ($this->orderBy) {
            $params['order'] = $this->orderBy;
        }

        if ($this->limit) {
            $params['limit'] = $this->limit;
        }

        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => "Bearer {$this->key}",
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ])->get($url, $params);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    public function first()
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    protected function mapOperator(string $operator)
    {
        $operators = [
            '=' => 'eq',
            '!=' => 'neq',
            '>' => 'gt',
            '>=' => 'gte',
            '<' => 'lt',
            '<=' => 'lte',
            'like' => 'like',
            'ilike' => 'ilike',
        ];

        return $operators[$operator] ?? 'eq';
    }
}
