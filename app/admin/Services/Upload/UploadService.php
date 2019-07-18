<?php

namespace Admin\Services\Upload;

use Common\Models\Upload\Upload;

class UploadService extends \Common\Services\Upload\UploadService
{
    /**
     * 创建文件上传记录
     * @param $params
     * @return bool|Upload
     */
    public function create($params)
    {
        $attributes = [
            //todo 用户id通过当前登录用户获取
            'user_id' => 0,
            'root_type' => Upload::ROOT_TYPE_BAC,
            'type' => array_get($params, 'type', Upload::TYPE_DEFAULT),
            'storage' => $params['storage'],
            'filename' => $params['filename'],
            'path' => str_start($params['path'], '/'),
            'ext_info' => array_get($params, 'ext_info', ''),
        ];

        return Upload::model(Upload::SCENARIO_CREATE)->saveModel($attributes);
    }

    /**
     * 获取文件列表
     * @param null $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList($params = null)
    {
        $list = Upload::model()->search($params)->paginate();

        foreach ($list as $item) {
            $item->getText();
        }

        return $list;
    }

    public function delete($id)
    {
        $upload = Upload::model()->getById($id);

        if (!$upload) {
            return $this->outputError('文件不存在');
        }
        Upload::model()->deleteById($id);

        if ($upload->storage == Upload::STORAGE_LOCAL) {
            $this->deleteLocalFile($upload->path);
        }

        return $this->outputSuccess('删除成功');
    }
}
