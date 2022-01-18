<?php
declare (strict_types=1);

namespace cores;

use think\Model;
use think\db\Query;
use cores\traits\ErrorTrait;

/**
 * 模型基类
 * Class BaseModel
 * @package cores
 */
abstract class BaseModel extends Model
{
    use ErrorTrait;

    // 定义表名
    protected $name;

    // 模型别名
    protected $alias = '';


    // 错误信息
    protected $error = '';

    /**
     * 模型基类初始化
     */
    public static function init()
    {
        parent::init();
    }

    /**
     * 查找单条记录
     * @param $data
     * @param array $with
     * @return array|false|static|null
     */
    public static function get($data, array $with = [])
    {
        try {
            $query = (new static)->with($with);
            return is_array($data) ? $query->where($data)->find() : $query->find((int)$data);
        } catch (\Exception $e) {
            return false;
        }
    }

}
