<?php
declare (strict_types=1);

namespace app\api\controller;

use cores\BaseController;

/**
 * API控制器基类
 * Class Controller
 * @package app\api\controller
 */
class Controller extends BaseController
{
	
	/**
	 * 返回封装后的 API 数据到客户端
	 * @param int|null $status 状态码
	 * @param string $message
	 * @param array $data
	 * @return array|Json
	 */
	protected function renderJson(int $status = null, string $message = '', array $data = [])
	{
	    return json(compact('status', 'message', 'data'));
	}
	/**
	 * 返回操作成功json
	 * @param array|string $data
	 * @param string $message
	 * @return array
	 */
	protected function renderSuccess($data = [], string $message = 'success')
	{
	    if (is_string($data)) {
	        $message = $data;
	        $data = [];
	    }
	    return $this->renderJson(config('status.success'), $message, $data);
	}
	/**
	 * 返回操作失败json
	 * @param string $message
	 * @param array $data
	 * @return array
	 */
	protected function renderError(string $message = 'error', array $data = [])
	{
	    return $this->renderJson(config('status.error'), $message, $data);
	}
	
	
    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postData($key = null)
    {
        return $this->request->post(is_null($key) ? '' : $key . '/a');
    }
    
    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postForm($key = 'form')
    {
        return $this->postData($key);
    }
}
