<?php
declare (strict_types=1);

namespace app\common\library\wechat;

class WXBizDataCrypt
{
    private $appid;
    private $sessionKey;

    /**
     * 构造函数
     * @param string $appid string 小程序的appid
     * @param string $sessionKey string 用户在小程序登录后获取的会话密钥
     */
    public function __construct(string $appid, string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param string $encryptedData 加密的用户数据
     * @param string $iv 与用户数据一同返回的初始向量
     * @param mixed $content 解密后的原文
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData(string $encryptedData, string $iv, &$content): int
    {
        if (strlen($this->sessionKey) != 24) {
            return ErrorCode::$IllegalAesKey;
        }
        if (strlen($iv) != 24) {
            return ErrorCode::$IllegalIv;
        }
        $aesKey = base64_decode($this->sessionKey);
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV);

        if (empty($result)) {
            return ErrorCode::$IllegalBuffer;
        }
        $resultArr = json_decode($result, true);
        if (empty($resultArr)) {
            return ErrorCode::$IllegalBuffer;
        }
        if ($resultArr['watermark']['appid'] != $this->appid) {
            return ErrorCode::$IllegalBuffer;
        }
        $content = $resultArr;
        return ErrorCode::$OK;
    }
}

