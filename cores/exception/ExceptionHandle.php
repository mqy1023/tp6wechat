<?php
namespace cores\exception;

use Throwable;
use think\Response;
use think\response\Json;
use think\facade\Log;
use think\facade\Request;
use think\exception\Handle;
use think\db\exception\PDOException;
use think\exception\HttpResponseException;
use cores\exception\BaseException;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
	
	// 状态码
	private $status;
	
	// 错误信息
	private $message;
	
	// 附加数据
	public $data = [];
	
	
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        // HttpException::class,
        // HttpResponseException::class,
        // ModelNotFoundException::class,
        // DataNotFoundException::class,
        // ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        // parent::report($exception);
    }
	
	/**
	 * Render an exception into an HTTP response.
	 *
	 * @access public
	 * @param $request
	 * @param Throwable $e
	 * @return Response
	 */
	public function render($request, Throwable $e): Response
	{
	    if ($e instanceof HttpResponseException) {
	        return $e->getResponse();
	    }
	    // 手动触发的异常 BaseException
	    if ($e instanceof BaseException) {
	        $this->status = $e->status;
	        $this->message = $e->message;
	        $this->data = $e->data;
	        $extend = property_exists($e, 'extend') ? $e->extend : [];
	        return $this->output($extend);
	    }
	    // 系统运行的异常
	    $this->status = config('status.error');
	    $this->message = $e->getMessage() ?: '很抱歉，服务器内部错误';
	    // 如果是debug模式, 输出调试信息
	    if (is_debug()) {
	        return $this->outputDebug($e);
	    }
	    // 将运行异常写入日志
	    $this->errorLog($e);
	    return $this->output();
	}
	
	/**
	 * 返回json格式数据
	 * @param array $extend 扩展的数据
	 * @return Json
	 */
	private function output(array $extend = []): Json
	{
	    $jsonData = ['message' => $this->message, 'status' => $this->status, 'data' => $this->data];
	    return json(array_merge($jsonData, $extend));
	}
	
	/**
	 * 返回json格式数据 (debug模式)
	 * @param Throwable $e
	 * @return Json
	 */
	private function outputDebug(Throwable $e): Json
	{
	    $debug = [
	        'name' => get_class($e),
	        'file' => $e->getFile(),
	        'line' => $e->getLine(),
	        'code' => $this->getCode($e),
	        'message' => $this->getMessage($e),
	        'trace' => $e->getTrace(),
	        'source' => $this->getSourceCode($e),
	    ];
	    return $this->output(['debug' => $debug]);
	}
	
	/**
	 * 将异常写入日志
	 * @param Throwable $e
	 */
	private function errorLog(Throwable $e)
	{
	    // 错误信息
	    $data = [
	        'file' => $e->getFile(),
	        'line' => $e->getLine(),
	        'message' => $this->getMessage($e),
	        'status' => $this->getCode($e),
	    ];
	    // 日志内容
	    $log = getVisitor();
	    $log .= "\r\n" . "[ message ] [{$data['status']}] {$data['message']}";
	    $log .= "\r\n" . "[ file ] {$data['file']}:{$data['line']}";
	    // $log .= "\r\n" . "[ time ] " . format_time(time());
	    $log .= "\r\n" . '[ header ] ' . print_r(Request::header(), true);
	    $log .= "" . '[ param ] ' . print_r(Request::param(), true);
	    // 如果是数据库报错, 则记录sql语句
	    if ($e instanceof PDOException) {
	        $log .= "[ Error SQL ] " . $e->getData()['Database Status']['Error SQL'];
	        $log .= "\r\n";
	    }
	    $log .= "\r\n" . $e->getTraceAsString();
	    $log .= "\r\n" . '--------------------------------------------------------------------------------------------';
	    // 写入日志文件
	    Log::record($log, 'error');
	}
	
}
