<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/21
 * Time: 15:37
 * author: soliang
 */

namespace Common\Models;

use Common\Traits\Model\StaticModel;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use StaticModel;

    /**
     * 获取 model 连接信息
     * @return array
     */
    public static function getModelInfo()
    {
        $model = self::model();
        return [
            'table' => $model->getTable(),
            'connection' => $model->getConnection()->getName(),
            'key' => $model->getKeyName(),
        ];
    }

    /**
     * 获取 connection.table 字符串
     * @return string
     */
    public static function getConnectionDotTable()
    {
        $info = self::getModelInfo();
        return $info['connection'] . '.' . $info['table'];
    }
}
