<?php
declare (strict_types=1);

namespace cores\middleware;

use think\Response;
use think\facade\Log as FacadeLog;

/**
 * 中间件：应用日志
 */
class AppLog
{
    // 访问日志
    private static $beginLog = '';

    /**
     * 前置中间件
     * @param \think\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(\think\Request $request, \Closure $next)
    {
        // 记录访问日志
        if (env('begin_log')) {
            $log = getVisitor($request);
            $log .= "\r\n" . '[ header ] ' . print_r($request->header(), true);
            $log .= "" . '[ param ] ' . print_r($request->param(), true);
            $log .= '--------------------------------------------------------------------------------------------';
            static::$beginLog = $log;
        }
        return $next($request);
    }
	
	/**
	 * 记录访问日志
	 * @param Response $response
	 */
	public function end(Response $response)
	{
	    FacadeLog::record(static::$beginLog, 'begin');
	}
}