<?php

namespace Common\Services\Upload;

use Common\Models\Upload\Upload;
use Common\Services\BaseService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService extends BaseService
{
    /**
     * 保存文件至本地
     * @param UploadedFile $file
     * @return array
     */
    public function moveFile(UploadedFile $file)
    {
        $attributes = [];
        $ext = $file->getClientOriginalExtension();
        $attributes['filename'] = $file->getClientOriginalName();
        $filename = $this->getUniqueFileName() . '.' . $ext;
        $attributes['path'] = 'uploads/' . date('Ymd');
        $file->move($this->getLocalStorage($attributes['path']), $filename);
        $attributes['path'] = str_finish($attributes['path'], '/') . $filename;
        $attributes['storage'] = Upload::STORAGE_LOCAL;
        return $attributes;
    }

    /**
     * 生成文件名
     * @return string
     */
    public function getUniqueFileName()
    {
        return uniqid() . Str::random(6);
    }

    /**
     * 获取本地存储路径
     * @param string $path
     * @return string
     */
    public function getLocalStorage($path = 'uploads')
    {
        return public_path($path);
    }

    public function deleteLocalFile($path)
    {
        $path = $this->getLocalStorage($path);
        @unlink($path);
    }
}
