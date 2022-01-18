<?php
// 应用公共文件

use think\facade\Env;
use think\facade\Request;
use cores\exception\BaseException;

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

/**
 * 模拟GET请求 HTTPS的页面
 * @param string $url 请求地址
 * @param array $data
 * @return string $result
 * @throws \cores\exception\BaseException
 */
function getHttp(string $url, array $data = [])
{
	// 处理query参数
	if (!empty($data)) {
		$url = $url . '?' . http_build_query($data);
	}
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
	$result = curl_exec($curl);
	if ($result === false) {
		throwError(curl_error($curl));
	}
	curl_close($curl);
	return $result;
}

/**
 * 输出报错信息
 * @param string $message
 * @throws Exception
 */
function throwError(string $message): void
{
	throw new BaseException(array('message' => $message));
}


/**
 * 获取全局唯一标识符
 * @param bool $trim
 * @return string
 */
function get_guid_v4(bool $trim = true): string
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        $charid = com_create_guid();
        return $trim == true ? trim($charid, '{}') : $charid;
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand(intval((double)microtime() * 10000));
    $charid = strtolower(md5(uniqid((string)rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    return $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
}

/**
 * 隐藏手机号中间四位 13012345678 -> 130****5678
 * @param string $mobile 手机号
 * @return string
 */
function hide_mobile(string $mobile): string
{
    return substr_replace($mobile, '****', 3, 4);
}
