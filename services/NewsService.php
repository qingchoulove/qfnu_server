<?php

namespace services;
use common\Util;
use models\NewsModel;

class NewsService extends BaseService {


    public function getNewsById(string $id): array {
        $data = NewsModel::where('new_id', $id)
            ->first();
        return empty($data) ? null : $data->toArray();
    }

    public function getNewsByType(int $type, int $offset, int $limit): array {
        $data = NewsModel::where('type', $type)
            ->orderBy('apply_time', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        return empty($data) ? null : $data->toArray();
    }

}