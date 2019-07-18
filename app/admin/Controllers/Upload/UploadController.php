<?php

namespace Admin\Controllers\Test;

use Admin\Controllers\BaseController;
use Admin\Rules\Upload\UploadRule;
use Admin\Services\Upload\UploadService;

class UploadController extends BaseController
{
    /**
     * 上传文件
     * @param UploadRule $rule
     * @return array
     */
    public function create(UploadRule $rule)
    {
        $params = $this->getParams();
        if (!$rule->validate(UploadRule::SCENARIO_CREATE, $params)) {
            return $this->resultFail($rule->getError());
        }

        if (!$attributes = UploadService::server()->moveFile($this->request->file('file'))) {
            return $this->resultFail('上传文件保存失败');
        }

        if (!$model = UploadService::server()->create(array_merge($params, $attributes))) {
            return $this->resultFail('上传文件保存记录失败');
        }

        return $this->resultSuccess($model->getText(), '上传成功');
    }

    /**
     * 获取文件列表
     * @return array
     */
    public function list()
    {
        return $this->resultSuccess(UploadService::server()->getList(), '获取成功');
    }

    /**
     * 文件删除
     * @return array
     */
    public function delete()
    {
        $id = $this->getParam('id');

        $deleteResult = UploadService::server()->delete($id);

        return $this->resultJudge(
            $deleteResult->isSuccess(),
            $deleteResult->getMsg(),
            $deleteResult->getMsg()
        );
    }
}
