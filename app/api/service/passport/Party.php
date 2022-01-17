<?php
declare (strict_types=1);

namespace app\api\service\passport;

use app\api\model\UserOauth as UserOauthModel;
use app\api\service\user\Oauth as OauthService;
use app\common\service\BaseService;
use cores\exception\BaseException;

/**
 * 第三方用户注册登录服务
 * Class Party
 * @package app\api\service\passport
 */
class Party extends BaseService
{
    /**
     * 保存用户的第三方认证信息
     * @param int $userId 用户ID
     * @param array $partyData 第三方登录信息
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createUserOauth(int $userId, array $partyData = []): bool
    {
        try {
            // 获取oauthId和unionId
            $oauthInfo = $this->getOauthInfo($partyData);
        } catch (BaseException $e) {
            // isBack参数代表需重新获取code, 前端拿到该参数进行页面返回
            throwError($e->getMessage(), null, ['isBack' => true]);
            return false;
        }
        // 是否存在第三方用户
        $oauthId = UserOauthModel::getOauthIdByUserId($userId, $partyData['oauth']);
        // 如果不存在oauth则写入
        if (empty($oauthId)) {
            return (new UserOauthModel)->add([
                'user_id' => $userId,
                'oauth_type' => $partyData['oauth'],
                'oauth_id' => $oauthInfo['oauth_id'],
                'unionid' => $oauthInfo['unionid'] ?? '',   // unionid可以不存在
            ]);
        }
        // 如果存在第三方用户, 需判断oauthId是否相同
        if ($oauthId != $oauthInfo['oauth_id']) {
            // isBack参数代表需重新获取code, 前端拿到该参数进行页面返回
            throwError('很抱歉，当前手机号已绑定其他微信号', null, ['isBack' => true]);
        }
        return true;
    }

    /**
     * 获取微信小程序登录态(session)
     * 这里支持静态变量缓存, 用于实现第二次调用该方法时直接返回已获得的session
     * @param string $code
     * @return array|false
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getMpWxSession(string $code)
    {
        static $session;
        if (empty($session)) {
            try {
                // 微信小程序通过code获取session
                $session = OauthService::wxCode2Session($code);
            } catch (BaseException $e) {
                // showError参数表示让前端显示错误
                throwError($e->getMessage());
                return false;
            }
        }
        return $session;
    }

    /**
     * 第三方用户信息
     * @param array $partyData 第三方用户信息
     * @param bool $isGetAvatarUrl 是否下载头像
     * @return array
     * @throws BaseException
     * @throws \think\Exception
     */
    public static function partyUserInfo(array $partyData, bool $isGetAvatarUrl = true): array
    {
        $partyUserInfo = $partyData['userInfo'];
        $data = [
            'nick_name' => $partyUserInfo['nickName'],
            'gender' => $partyUserInfo['gender']
        ];
        // 下载用户头像
        if ($isGetAvatarUrl) {
            $data['avatar_id'] = static::partyAvatar($partyUserInfo['avatarUrl']);
        }
        return $data;
    }


    /**
     * 获取第三方用户session信息 (openid、unionid、session_key等)
     * @param array $partyData
     * @return array|null
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getOauthInfo(array $partyData): ?array
    {
        if ($partyData['oauth'] === 'MP-WEIXIN') {
            $wxSession = static::getMpWxSession($partyData['code']);
            return ['oauth_id' => $wxSession['openid'], 'unionid' => $wxSession['unionid'] ?? null];
        }
        return null;
    }
}