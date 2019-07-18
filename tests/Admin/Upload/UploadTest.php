<?php

namespace Tests\Admin\Upload;

use Common\Models\Upload\Upload;
use Illuminate\Http\UploadedFile;
use Tests\Admin\AdminTestBase;

class UploadTest extends AdminTestBase
{
    /**
     * 上传文件
     */
    public function testCreate()
    {
        $filePath = base_path('tests\Admin\Upload\test.jpeg');
        $tmpPath = base_path('tests\Admin\Upload\tmp.jpeg');
        copy($filePath, $tmpPath);
        $files = [
            'file' => new UploadedFile(
                $tmpPath,
                basename($tmpPath),
                'image/jpeg',
                UPLOAD_ERR_OK,
                true
            ),
        ];

        $params = [
            'type' => Upload::TYPE_DEFAULT,
        ];

        $this->call('POST', 'api/admin/upload/upload/create', $params, [], $files, []);
        @unlink($tmpPath);

        $this->getData();
    }

    public function testList()
    {
        $params = [
            'page_size' => 5,
        ];

        $this->json('GET', 'api/admin/upload/upload/list', $params)->getData();
    }

    public function testDelete()
    {
        $params = [
            'id' => 1,
        ];
        $this->json('POST', 'api/admin/upload/upload/delete', $params)
            ->getData();
    }
}
