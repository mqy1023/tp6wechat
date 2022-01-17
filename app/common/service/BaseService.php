<?php
declare (strict_types=1);

namespace app\common\service;

use cores\traits\ErrorTrait;
use think\facade\Request;

/**
 * 系统服务基础类
 * Class BaseService
 * @package app\common\service
 */
class BaseService
{
	// 错误信息Trait
    use ErrorTrait;

    // 请求管理类
    /* @var $request \think\Request */
    protected $request;

    /**
     * 构造方法
     * BaseService constructor.
     */
    public function __construct()
    {
        // 请求管理类
        $this->request = Request::instance();
        // 执行子类的构造方法
        $this->initialize();
    }

    /**
     * 构造方法 (供继承的子类使用)
     */
    protected function initialize()
    {
    }
}
