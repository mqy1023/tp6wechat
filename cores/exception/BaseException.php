<?php
declare (strict_types=1);

namespace cores\exception;

use think\Exception;

/**
 * 自定义异常类的基类
 * Class BaseException
 * @package cores\exception
 */
class BaseException extends Exception
{
    // 状态码
    public $status;

    // 错误信息
    public $message = '';

    // 输出的数据
    public $data = [];

    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含status、msg、data，且不应该是空值
     */
    public function __construct($params = [])
    {
		if(!is_array($params)){
			return;
		}
        parent::__construct();
        $this->status = $params['status'] ?? config('status.error');
        $this->message = $params['message'] ?? '很抱歉，服务器内部错误';
        $this->data = $params['data'] ?? [];
    }
}

