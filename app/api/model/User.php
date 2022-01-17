<?php
declare (strict_types=1);

namespace app\api\model;

use think\facade\Cache;
use app\api\service\User as UserService;
use app\api\model\UserOauth as UserOauthModel;
use think\Model;
use cores\exception\BaseException;

/**
 * 用户模型类
 * Class User
 * @package app\api\model
 */
class User extends Model
{
	
	// 定义表名
	protected $name = 'user';
	
	// 定义主键
	protected $pk = 'user_id';
	
	// 性别
	private $gender = [0 => '未知', 1 => '男', 2 => '女'];
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'create_time',
        'update_time'
    ];

    /**
     * 获取器：隐藏手机号中间四位
     * @param string $value
     * @return string
     */
    public function getMobileAttr(string $value): string
    {
        return strlen($value) === 11 ? hide_mobile($value) : $value;
    }
	
	
	/**
	 * 获取用户信息
	 * @param $where
	 * @param array $with
	 * @return static|array|false|null
	 */
	public static function detail($where, array $with = [])
	{
	    $filter = ['is_delete' => 0];
	    if (is_array($where)) {
	        $filter = array_merge($filter, $where);
	    } else {
	        $filter['user_id'] = (int)$where;
	    }
	    return static::get($filter, $with);
	}

    /**
     * 获取用户信息
     * @param string $token
     * @return User|array|false|null
     * @throws BaseException
     */
    public static function getUserByToken(string $token)
    {
        // 检查登录态是否存在
        if (!Cache::has($token)) {
            return false;
        }
        // 用户的ID
        $userId = (int)Cache::get($token)['user']['user_id'];
        // 用户基本信息
        $userInfo = self::detail($userId);
        if (empty($userInfo) || $userInfo['is_delete']) {
            throwError('很抱歉，用户信息不存在或已删除', config('status.not_logged'));
        }
        // 获取用户关联的第三方用户信息(当前客户端)
        try {
            $userInfo['currentOauth'] = UserOauthModel::getOauth($userId, getPlatform());
        } catch (\Throwable $e) {
            throwError($e->getMessage());
        }
        return $userInfo;
    }

    /**
     * 绑定手机号(当前登录用户)
     * @param array $data
     * @return bool
     * @throws BaseException
     */
    public function bindMobile(array $data): bool
    {
        // 当前登录的用户信息
        $userInfo = UserService::getCurrentLoginUser(true);
        // 验证绑定的手机号
        $this->checkBindMobile($data);
        // 更新手机号记录
        return $userInfo->save(['mobile' => $data['mobile']]);
    }

    /**
     * 验证绑定的手机号
     * @param array $data
     * @return void
     * @throws BaseException
     */
    private function checkBindMobile(array $data): void
    {
        // 验证短信验证码是否匹配
        // if (!CaptchaApi::checkSms($data['smsCode'], $data['mobile'])) {
        //     throwError('短信验证码不正确');
        // }
		$cacheCode = cache(config("sms.sms_pre").$data['mobile']);
		if(empty($cacheCode) || $cacheCode  != $data['smsCode']) {
			throwError('短信验证码不正确');
		}
        // 判断手机号是否已存在
        if (static::checkExistByMobile($data['mobile'])) {
            throwError('很抱歉，该手机号已绑定其他账户');
        }
    }
	
	
	/**
	 * 指定的手机号是否已存在
	 * @param string $mobile
	 * @return bool
	 */
	public static function checkExistByMobile(string $mobile): bool
	{
	    $model = new static;
	    return (bool)$model->where('mobile', '=', $mobile)
	        ->where('is_delete', '=', 0)
	        ->value($model->getPk());
	}
}
