<?php
namespace services;

use common\Util;
use models\NewsModel;
use common\exceptions\UserException;

class NewsService extends BaseService
{

    /**
     * 查看新闻详情
     * @param  string
     * @return array
     */
    public function getNewsById(string $id): array
    {
        $data = NewsModel::where(['new_id' => $id, 'is_del' => 0])
            ->first();
        return empty($data) ? null : $data->toArray();
    }

    /**
     * 获取新闻列表
     * @param  int
     * @param  int
     * @param  int
     * @return array
     */
    public function getNewsByType(int $type, int $offset, int $limit): array
    {
        $data = NewsModel::select('new_id', 'title', 'content', 'url', 'apply_time')
            ->where(['type' => $type, 'is_del' => 0])
            ->orderBy('apply_time', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        return empty($data) ? [] : $data->toArray();
    }

    /**
     * 添加新闻
     *
     * @param array $new
     * @return void
     */
    public function addNew(array $new):string
    {
        $model = new NewsModel();
        foreach ($new as $key => $value) {
            $model -> $key = $value;
        }
        $model->new_id = Util::UUID();
        $model->save();
        return $model->new_id;
    }

    /**
     * 更新文章
     *
     * @param array $new
     * @return void
     */
    public function updateNew(array $new)
    {
        $model = NewsModel::where(['new_id' => $new['new_id'], 'is_del' => 0])
            ->first();
        if (empty(model)) {
            throw new UserException('文章不存在');
        }
        foreach ($new as $key => $value) {
            $model->$key = $value;
        }
        $model->save();
    }

    /**
     * 删除文章
     *
     * @param string $newId
     * @return void
     */
    public function deleteNew(string $newId)
    {
        $model = NewsModel::where(['new_id' => $newId, 'is_del' => 0])
            ->update(['is_del' => 1]);
    }
}
