<?php

namespace Common\Models\Upload;

use Common\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Upload extends BaseModel
{
    /** 来源端类型：管理后台 */
    const ROOT_TYPE_BAC = 1;
    /** 来源端类型：前台 */
    const ROOT_TYPE_FRO = 2;
    /** 来源端类型 */
    const ROOT_TYPE = [
        self::ROOT_TYPE_BAC => '管理后台',
        self::ROOT_TYPE_FRO => '前台',
    ];
    /** 类型：默认 */
    const TYPE_DEFAULT = 1;
    /** 类型 */
    const TYPE = [
        self::TYPE_DEFAULT => '默认',
    ];
    /** 存储方式：本地 */
    const STORAGE_LOCAL = 'local';
    /** 存储方式：OSS */
    const STORAGE_OSS = 'oss';
    /** 存储方式 */
    const STORAGE = [
        self::STORAGE_LOCAL => '本地',
        self::STORAGE_OSS => 'OSS',
    ];
    /** 状态：正常 */
    const STATUS_NORMAL = 1;
    /** 状态：已删除 */
    const STATUS_DELETE = -1;
    /** 状态：文件已清除 */
    const STATUS_CLEARED = -2;
    /** 状态 */
    const STATUS = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_DELETE => '已删除',
        self::STATUS_CLEARED => '已清除',
    ];
    /** 场景：创建 */
    const SCENARIO_CREATE = 'create';
    /** 全局约束：排除已清理文件 */
    const SCOPE_STATUS_CLEARED = 'status_cleared';
    /** 允许上传文件类型 */
    const ALLOW_EXTENSION = [
        'jpg',
        'jpeg',
        'png',
        'pdf',
        'csv',
    ];
    protected $table = 'upload';
    protected $fillable = [];
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(self::SCOPE_STATUS_CLEARED, function (Builder $builder) {
            $builder->where('status', '!=', self::STATUS_CLEARED);
        });
    }

    public function safes()
    {
        return [
            self::SCENARIO_CREATE => [
                'user_id',
                'root_type',
                'source_id',
                'type',
                'storage',
                'filename',
                'path',
                'ext_info',
                'status' => self::STATUS_NORMAL,
            ],
        ];
    }

    public function textRules()
    {
        return [
            'array' => [
                'root_type' => self::ROOT_TYPE,
                'type' => self::TYPE,
                'storage' => self::STORAGE,
                'status' => self::STATUS,
            ],
            'function' => [
                'path' => function () {
                    return config('app.url') . str_start($this->path, '/');
                }
            ]
        ];
    }

    public function search($params = null)
    {
        $query = self::query();

        return $query;
    }

    /**
     * 根据id获取model
     * @param $id
     * @param array $with
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|object|null
     */
    public function getById($id, $with = [])
    {
        $ids = (array)$id;
        $query = self::query()->with($with)
            ->whereIn('id', $ids);

        if (is_array($id)) {
            $result = $query->get();
        } else {
            $result = $query->first();
        }
        return $result;
    }

    public function deleteById($id)
    {
        $update = [
            'status' => self::STATUS_DELETE,
        ];
        return self::query()->where('id', $id)->update($update);
    }
}
