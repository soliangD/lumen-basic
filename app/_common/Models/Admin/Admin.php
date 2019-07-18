<?php

namespace Common\Models\Admin;

use Common\Models\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends BaseModel implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /** @var int 状态：删除 */
    const STATUS_DELETE = -2;
    /** @var int 状态：禁用 */
    const STATUS_DISABLED = -1;
    /** @var int 状态：正常 */
    const STATUS_NORMAL = 1;
    /** @var array 状态 */
    const STATUS = [
        self::STATUS_DELETE => '删除',
        self::STATUS_DISABLED => '禁用',
        self::STATUS_NORMAL => '正常',
    ];
    /** 全局约束：排除已删除的用户 */
    const SCOPE_STATUS_DELETE = 'status_delete';
    /** @var string 场景：创建 */
    const SCENARIO_CREATE = 'create';
    protected $table = 'admin';
    protected $fillable = [];
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();
//        static::addGlobalScope(self::SCOPE_STATUS_DELETE, function (Builder $builder) {
//            $builder->where('status', '!=', self::STATUS_DELETE);
//        });
    }

    public function safes()
    {
        return [
            self::SCENARIO_CREATE => [
                'username',
                'email',
                'password_hash',
                'status' => self::STATUS_NORMAL,
            ],
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
        ];
    }

    /***************************************** 分割线 ☝ base ☟ append ************************************************/

}
