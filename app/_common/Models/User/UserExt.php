<?php

namespace Common\Models\User;

use Common\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class UserExt extends BaseModel
{
    /** @var string key 注册确认时间 */
    const KEY_REGISTER_CONFIRM_TIME = 'register_confirm_time';
    /** @var array KEY */
    const KEY = [
        self::KEY_REGISTER_CONFIRM_TIME => '注册确认时间',
    ];
    /** @var string 场景：创建 */
    const SCENARIO_CREATE = 'create';
    protected $table = 'user_ext';
    protected $fillable = [];

    public function safes()
    {
        return [
            self::SCENARIO_CREATE => [
                'user_id',
                'key',
                'value',
            ],
        ];
    }

    /**
     * @param $userId
     * @param $params
     * @return mixed
     * @throws \Throwable
     */
    public static function batchUpdOrAdd($userId, $params)
    {
        return DB::transaction(function () use ($userId, $params) {
            foreach ($params as $key => $value) {
                self::updOrAdd($userId, $key, $value);
            }
            return true;
        });
    }

    /***************************************** 分割线 ☝ base ☟ append ************************************************/

    /**
     * @param $userId
     * @param $key
     * @param $value
     * @return bool|UserExt
     */
    public static function updOrAdd($userId, $key, $value)
    {
        if (!in_array($key, array_keys(self::KEY))) {
            return false;
        }
        return self::updateOrCreateModel(
            self::SCENARIO_CREATE,
            [
                'user_id' => $userId,
                'key' => $key,
            ],
            ['value' => $value]
        );
    }
}
