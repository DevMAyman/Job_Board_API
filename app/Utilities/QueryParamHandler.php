<?php

namespace App\Utilities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QueryParamHandler
{
    public static function handle(Builder $query, array $params, $searchField=null)
    {
        $maxLimit = 50;
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? min($maxLimit, max(1, (int)$params['limit'])) : 10;
        $order_by = isset($params['order']) ? $params['order'] : null;
        $search = isset($params['search']) ? $params['search'] : null;
        if (!$searchField){
            $searchField = isset($params['searchField']) ? $params['searchField'] : 'name';
        }

        // Apply search filter if search query and searchField are provided
        if ($search && $searchField) {
            // dd($searchField, $search);
            $query->where($searchField, 'LIKE', "%{$search}%");
        }

        // Apply additional filters
        foreach ($params as $key => $value) {
            if (!in_array($key, ['page', 'limit', 'order', 'search', 'searchField'])) {
                $query->where($key, $value);
            }
        }

        // Apply sorting
        if ($order_by) {
            if (Str::startsWith($order_by, '-')) {
                $field = substr($order_by, 1);
                $query->orderBy($field, 'desc');
            } else {
                $query->orderBy($order_by);
            }
        }

        // Paginate the results
        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'total_pages' => $paginator->lastPage(),
            'total_count' => $paginator->total(),
        ];
    }
}
