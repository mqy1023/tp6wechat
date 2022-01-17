<?php
declare (strict_types=1);

namespace app\common\library\wechat;

use think\facade\Cache;
use cores\traits\ErrorTrait;

/**
 * 微信api基类
 * Class wechat
 * @package app\library
 */
class WxBase
{
    use ErrorTrait;

    protected $appId;
    protected $appSecret;

    /**
     * 构造函数
     * WxBase constructor.
     * @param $appId
     * @param $appSecret
     */
    public function __construct($appId = null, $appSecret = null)
    {
        $this->setConfig($appId, $appSecret);
    }

    protected function setConfig($appId = null, $appSecret = null)
    {
        !empty($appId) && $this->appId = $appId;
        !empty($appSecret) && $this->appSecret = $appSecret;
    }

    /**
     * 获取access_token
     * @return mixed
     * @throws \cores\exception\BaseException
     */
    protected function getAccessToken()
    {
        $cacheKey = $this->appId . '@access_token';
        if (!Cache::get($cacheKey)) {
            // 请求API获取 access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
            $result = $this->get($url);
            $response = json_decode($result, true);
            if (array_key_exists('errcode', $response)) {
                throwError("access_token获取失败，错误信息：{$result}");
            }
            // 记录日志
            log_record([
                'name' => '获取access_token',
                'url' => $url,
                'appId' => $this->appId,
                'result' => $result
            ]);
            // 写入缓存
            Cache::set($cacheKey, $response['access_token'], 100 * 60); // 2小时内，2 * 60 * 60
        }
        return Cache::get($cacheKey);
    }

}
