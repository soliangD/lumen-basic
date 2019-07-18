<?php

namespace Common\Traits\Model;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Trait StaticModel
 * @package Common\Traits\Model
 */
trait StaticModel
{
    /**
     * 导出类
     */
    use ExportModel;
    /**
     * 获取器
     */
    use TextModel;
    /**
     * 那就叫排序器吧
     */
    use SortModel;
    /**
     * @var array
     */
    public $pageSizeAllow = [5, 10, 20, 30, 40];
    /**
     * @var string
     */
    protected $scenario;

    /**
     * @param $scenario
     * @param array $filter
     * @param array $values
     * @return static
     */
    public static function firstOrCreateModel($scenario = null, array $filter = [], $values = [])
    {
        $model = self::firstOrNewModel($scenario, $filter);
        if (!$model->exists) {
            $model->saveModel($values);
        }
        return $model;
    }

    /**
     * @param $scenario
     * @param array $filter
     * @return static
     */
    public static function firstOrNewModel($scenario = null, array $filter = [])
    {
        $classModel = static::class;

        $model = new $classModel();
        if (!is_null($instance = $model->where($filter)->first())) {
            $model = $instance;
        }
        foreach (array_keys($filter) as $attribute) {
            $model->$attribute = $filter[$attribute];
        }
        $model->setScenario($scenario);
        return $model;
    }

    /**
     * 保存模型 赋值属性
     * @param $attributes
     * @param bool $save
     * @return $this|bool
     */
    public function saveModel($attributes, $save = true)
    {
        if ($attributes !== false) {
            if ($this->getFillable()) {
                $this->fill($attributes);
            }
            $this->setDefaultAttributes($this->getSafeDefaultAttributes(), $attributes);
        }
        if ($save && !$this->save()) {
            return false;
        }

        return $this;
    }

    /**
     * 设置默认值
     * @param $attributes
     * @param null $values
     * @suppress PhanTypeMismatchArgumentInternal
     */
    public function setDefaultAttributes($attributes, $values = null)
    {
        foreach ($attributes as $key => $defaultValue) {
            if (is_object($defaultValue)) {
                $params = [$value = $values[$key] ?? null, $model = $this];
                $defaultValue = call_user_func_array($defaultValue, $params);
            }
            if ($defaultValue !== null) {
                $this->$key = $defaultValue;
            }
        }
    }

    /**
     * @return array
     */
    public function getSafeDefaultAttributes()
    {
        $safes = $this->safes();
        $attributes = $safes[$this->scenario] ?? [];
        $safeDefaultAttributes = [];
        foreach ($attributes as $key => $attribute) {
            if (!is_integer($key)) {
                $safeDefaultAttributes[$key] = $attribute;
            }
        }
        return $safeDefaultAttributes;
    }

    /**
     * @return array
     */
    public function safes()
    {
        return [];
    }

    /**
     *
     * @param $scenario
     * @param array $filter
     * @param array $values
     * @return static
     */
    public static function updateOrCreateModel($scenario = null, array $filter = [], $values = [])
    {
        return tap(self::firstOrNewModel($scenario, $filter), function ($instance) use ($values) {
            $instance->saveModel($values);
        });
    }

    /**
     * @param $scenario
     * @return static
     */
    public function setScenario($scenario)
    {
        if (is_array($scenario)) {
            $this->setSafeAttributes($scenario);
        } else {
            $this->scenario = (string)$scenario;
            $this->fillable($this->getSafeAttributes());
        }
        return $this;
    }

    /**
     * @param $attributes
     * @return $this
     */
    public function setSafeAttributes($attributes)
    {
        $fillAble = $this->getFillable();
        $this->fillable(array_keys($attributes));
        if ($attributes) {
            $this->fill($attributes);
        }
        $this->fillable($fillAble);
        return $this;
    }

    /**
     * @return array
     */
    public function getSafeAttributes()
    {
        $safes = $this->safes();
        $attributes = $safes[$this->scenario] ?? [];
        $safeAttributes = [];
        foreach ($attributes as $key => $attribute) {
            if (is_integer($key)) {
                $safeAttributes[] = $attribute;
            }
        }
        return $safeAttributes;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return Carbon::now()->timestamp;
    }

    public function getDate()
    {
        return Carbon::now()->toDateTimeString();
    }

    /**
     * @param $arrayAttributes
     * @param bool $save
     * @param bool $returnModel
     * @return array|bool
     * @throws \Exception
     */
    public function saveModels($arrayAttributes, $save = true, $returnModel = false)
    {
        $insertChunkData = [];
        foreach ($arrayAttributes as $attributes) {
            $insertChunkData[] = $this->saveModel($attributes, false)->getAttributes();
        }
        if ($returnModel) {
            return $this->createModels($insertChunkData, $save);
        }
        if (!$save) {
            return $insertChunkData;
        }
        return $this->insertChunk($insertChunkData);
    }

    /**
     * @param $arrayAttributes
     * @param bool $save
     * @param bool $throwError
     * @return array|bool
     * @throws \Exception
     */
    public static function createModels($arrayAttributes, $save = true, $throwError = false)
    {
        $models = [];
        DB::beginTransaction();
        try {
            foreach ($arrayAttributes as $attributes) {
                if (!$model = self::createModel($attributes, $save)) {
                    abort(500, '写入数据失败');
                }
                $models[] = $model;
            }
            DB::commit();
        } catch (HttpException $e) {
            DB::rollBack();
            if ($throwError) {
                abort($e->getStatusCode(), $e->getMessage());
            }
            Log::info($e->getStatusCode() . $e->getMessage());
            return false;
        }
        return $models;
    }

    /**
     * @param $attributes
     * @param bool $save
     * @return bool|mixed
     */
    public static function createModel($attributes, $save = true)
    {
        return self::model($attributes)->saveModel($attributes, $save);
    }

    /**
     * @param $scenario
     * @param array $attributes
     * @return static
     */
    public static function model($scenario = null, array $attributes = [])
    {
        $classModel = static::class;
        $model = new $classModel($attributes);
        $model->setScenario($scenario);
        return $model;
    }

    /**
     * @param $data
     * @param int $size
     * @param bool $throwError
     * @return bool
     * @throws \Exception
     */
    public function insertChunk($data, $size = 1000, $throwError = false)
    {
        $attributes = array_chunk($data, $size);
        DB::beginTransaction();
        try {
            foreach ($attributes as $attribute) {
                if (!$this->insert($attribute)) {
                    abort(500, '写入数据失败');
                }
            }
            DB::commit();
        } catch (HttpException $e) {
            DB::rollBack();
            if ($throwError) {
                abort($e->getStatusCode(), $e->getMessage());
            }
            Log::info($e->getStatusCode() . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param $perPage
     * @return mixed
     */
    public function setPerPage($perPage)
    {
        return $this->perPage = $perPage;
    }

    /**
     * @return mixed
     */
    public function getPerPage()
    {
        $perPage = app(Request::class)->get('page_size');
        if (in_array($perPage, $this->pageSizeAllow)) {
            $this->perPage = $perPage;
        }
        return $this->perPage;
    }

    /**
     * @param $string
     * @return mixed
     */
    public function dbRaw($string)
    {
        return Db::raw($string);
    }

    public function andFilterWhere(array $condition)
    {
        foreach ($condition as $key => $val) {
            if ($val) {
                $this->where($key, $val);
            }
        }
    }
}
