<?php

namespace Common\Models\User;

use Common\Models\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends BaseModel implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'user';
    protected $fillable = [];
    protected $hidden = ['id', 'password_hash'];

    /** @var int 状态：删除 */
    const STATUS_DELETE = -2;
    /** @var int 状态：禁用 */
    const STATUS_DISABLED = -1;
    /** @var int 状态：正常 */
    const STATUS_NORMAL = 1;
    /** @var int 状态：未激活 */
    const STATUS_NOT_ACTIVATED = 2;
    /** @var array 状态 */
    const STATUS = [
        self::STATUS_DELETE => '删除',
        self::STATUS_DISABLED => '禁用',
        self::STATUS_NORMAL => '正常',
        self::STATUS_NOT_ACTIVATED => '未激活',
    ];
    /** @var array 状态为有效 */
    const STATUS_GROUP_VALID = [
        self::STATUS_DISABLED,
        self::STATUS_NORMAL,
    ];

    /** 全局约束：排除已删除的用户 */
    const SCOPE_STATUS_DELETE = 'status_delete';

    /** @var string 场景：创建 */
    const SCENARIO_CREATE = 'create';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(self::SCOPE_STATUS_DELETE, function (Builder $builder) {
            $builder->where('status', '!=', self::STATUS_DELETE);
        });
    }

    public function safes()
    {
        return [
            self::SCENARIO_CREATE => [
                'username',
                'email',
                'password_hash',
                'status' => self::STATUS_NOT_ACTIVATED,
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeWhereValid($query)
    {
        return $query->whereIn('status', self::STATUS_GROUP_VALID);
    }

    public function isValid()
    {
        return in_array($this->status, self::STATUS_GROUP_VALID);
    }

    public function isNormal()
    {
        return $this->status == self::STATUS_NORMAL;
    }

    public function isNotActivated()
    {
        return $this->status == self::STATUS_NOT_ACTIVATED;
    }

    public function changeStatusToNormal()
    {
        $this->status = self::STATUS_NORMAL;
        return $this->save();
    }

    /***************************************** 分割线 ☝ base ☟ relations **********************************************/

    /**
     * 关丽娜 user_ext
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userExt()
    {
        return $this->hasMany(UserExt::class, 'user_id', 'id');
    }

    /***************************************** 分割线 ☝ relations ☟ append ********************************************/

    /**
     * @param $email
     * @param bool $needValid
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null|static
     */
    public static function getByEmail($email, $needValid = true)
    {
        $query = self::query()->where('email', $email);

        if ($needValid) {
            $query->whereValid();
        }

        return $query->first();
    }

    /**
     * get by id or id arr
     * @param $value
     * @param bool $needValid
     * @param string $field
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|object|null|static
     */
    public static function getBy($value, $needValid = true, $field = 'id')
    {
        $query = self::query()->whereIn($field, (array)$value);

        if ($needValid) {
            $query->whereValid();
        }

        if (is_array($value)) {
            return $query->get();
        }

        return $query->first();
    }

    public static function add(array $params, bool $save = true)
    {
        return self::model(self::SCENARIO_CREATE)->saveModel($params, $save);
    }
}
