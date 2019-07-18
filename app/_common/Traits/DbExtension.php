<?php
/**
 * Created by PhpStorm.
 * User: summer
 * Date: 2018-11-27
 * Time: 17:00
 */

namespace Common\Traits;

use DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;

/**
 * Trait DbExtension
 * @package Common\Traits
 * @phan-file-suppress PhanUndeclaredStaticMethod
 * @phan-file-suppress PhanUndeclaredMethod
 */
trait DbExtension
{
    /**
     * 批量更新表的值，防止阻塞
     * @note 生成的SQL语句如下：
     * UPDATE trade
     * SET `transaction_name` = CASE
     * WHEN `id` = ? THEN
     * ?
     * WHEN `id` = ? THEN
     * ?
     * ELSE
     * `transaction_name`
     * END,
     * `batch_no` = CASE
     * WHEN `id` = ? THEN
     * ?
     * WHEN `id` = ? THEN
     * ?
     * ELSE
     * `batch_no`
     * END
     * WHERE
     * `id` IN (?,?)
     * 对sql注入进行了处理
     * @param array $multipleData 批量更新数据，二维数组，以id列为条件列
     * @param $referenceColumn /
     * @param bool $blankToNull 空白格置空(不进行 else $field 操作)
     * @return bool|int
     */
    public function batchUpdate($multipleData = [], $referenceColumn = null, $blankToNull = false)
    {
        try {
            if (empty($multipleData)) {
                return true;
            }
            $multipleData = collect($multipleData);
            if ($referenceColumn === null) {
                $referenceColumn = $this->primaryKey;
            }
            $tableName = DB::getTablePrefix() . $this->getTable(); // 表名

            $whereIds = $multipleData->pluck($referenceColumn);
            if ($whereIds->isEmpty()) {
                throw new \InvalidArgumentException("argument 1 don't have field " . $referenceColumn);
            }

            $when = [];
            foreach ($multipleData->all() as $rows) {
                $whenValue = Arr::get($rows, $referenceColumn);
                if (!$whenValue) continue;
                foreach ($rows as $fieldName => $value) {
                    if ($fieldName == $referenceColumn) continue;
                    if ($value === '') $value = null;
                    $when[$fieldName][] = [
                        'sql' => "when `{$referenceColumn}` = ? then ? ",
                        'bind' => [$whenValue, $value]
                    ];
                }
            }

            // 拼接sql语句
            $updateSql = "update " . $tableName . " set ";
            $caseArr = [];
            $bindings = [];
            foreach ($when as $fieldName => $item) {
                $else = $blankToNull ? '' : " else `$fieldName`";
                $caseSql = "`{$fieldName}` = case ";
                foreach ($item as $i) {
                    $caseSql .= $i['sql'];
                    $bindings = array_merge($bindings, $i['bind']);
                }
                $caseSql .= "$else end ";
                $caseArr[] = $caseSql;
            }

            $caseStr = implode(',', $caseArr);
            $bindings = array_merge($bindings, $whereIds->all());
            $whereInStr = rtrim(str_repeat('?,', $whereIds->count()), ',');

            $updateSql .= "{$caseStr} where `{$referenceColumn}` in ({$whereInStr})";

            // 传入预处理sql语句和对应绑定数据
            return DB::connection($this->getConnectionName())->update($updateSql, $bindings);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @note 生成的SQL语句如下：
     * UPDATE `config`
     * SET `value` = CASE
     * WHEN id = '25' THEN
     * '审核信息一致，审核通过1'
     * WHEN id = '26' THEN
     * '审核信息不一致，上传凭证错2'
     * END,
     * `remark` = CASE
     * WHEN id = '25' THEN
     * '商户充值审核列表选项1'
     * WHEN id = '26' THEN
     * '商户充值审核列表选2'
     * END
     * WHERE
     * `id` IN ('25', '26', '26')
     * @param array $update
     * @param string $whenField
     * @param string $whereField
     * @param bool $blankToNull 空白格置空(不进行 else $field 操作)
     *
     * @return bool|int
     */
    public function batchUpdate2(array $update, $whenField = 'id', $whereField = 'id', $blankToNull = false)
    {
        $table = $this->getTable();
        $update = collect($update);
        // 判断需要更新的数据里包含有放入when中的字段和where的字段
        if ($update->pluck($whenField)->isEmpty() || $update->pluck($whereField)->isEmpty()) {
            throw new \InvalidArgumentException("argument 1 don't have field " . $whenField);
        }
        $when = [];
        // 拼装sql，相同字段根据不同条件更新不同数据
        foreach ($update->all() as $sets) {
            $whenValue = $sets[$whenField];
            foreach ($sets as $fieldName => $value) {
                if ($fieldName == $whenField) continue;
                if (is_null($value) || $value === '') {
                    $value = 'null';
                } else {
                    $value = "'$value'";
                }
                $when[$fieldName][] = "when `{$whenField}` = '{$whenValue}' then $value";
            }
        }

        $build = DB::table($table)->whereIn($whereField, $update->pluck($whereField));
        if ($connection = $this->getConnectionName()) {
            $build->connection($connection);
        }

        foreach ($when as $fieldName => &$item) {
            $else = $blankToNull ? '' : " else `$fieldName`";
            $item = DB::raw("case " . implode(' ', $item) . "$else end ");
        }

        return $build->update($when);
    }

    /**
     * @return mixed
     * @suppress PhanUndeclaredMethod
     */
    public function getDatabaseName()
    {
        $connection = $this->getConnectionName() ?: 'mysql';
        return config("database.connections.{$connection}.database");
    }

    /**
     * @param array $values
     * @return bool
     */
    public function replaceInto(array $values)
    {
        if (empty($values)) {
            return true;
        }
        $data = $this->buildInsertSql($values);
        $replaceSql = str_replace_first('insert', 'replace', $data['sql']);
        return static::query()->getConnection()->statement($replaceSql, $data['bindings']);
    }

    /**
     *
     * @param $values
     * @return array
     */
    protected function buildInsertSql($values)
    {
        if (!is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        /** @var \Illuminate\Database\Query\Builder $query */
        $query = static::query()->getQuery();
        $bindings = $this->cleanBindings(Arr::flatten($values, 1));
        return ['sql' => $query->grammar->compileInsert($query, $values), 'bindings' => $bindings];
    }

    /**
     * @param array $bindings
     * @return array
     */
    protected function cleanBindings(array $bindings)
    {
        return array_values(array_filter($bindings, function ($binding) {
            return !$binding instanceof Expression;
        }));
    }

    /**
     * @param array $values
     * @return bool
     */
    public function insertIgonre(array $values)
    {
        if (empty($values)) {
            return true;
        }
        $data = $this->buildInsertSql($values);
        $replaceSql = str_replace_first('insert', 'insert ignore ', $data['sql']);
        return static::query()->getConnection()->statement($replaceSql, $data['bindings']);
    }
}
