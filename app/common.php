<?php
// 应用公共文件

use think\facade\Env;
use think\facade\Request;

/**
 * 当前是否为调试模式
 * @return bool
 */
function is_debug(): bool
{
    return (bool)Env::instance()->get('APP_DEBUG');
}


/**
 * 获取请求路径信息
 * @return string
 */function getVisitor(): string
{
	$data = [Request::ip(), Request::method(), Request::url(true)];
	return implode(' ', $data);
}


/**
 * 获取当前访问的渠道(微信小程序、H5、APP等)
 * @return string|null
 */
function getPlatform()
{
    static $value = null;
    // 从header中获取 channel
    empty($value) && $value = request()->header('platform');
    // 调试模式下可通过param中获取
    if (is_debug() && empty($value)) {
        $value = request()->param('platform');
    }
    return $value;
}
