<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\service\User as UserService;
use app\api\model\User as UserModel;
use think\response\Json;

/**
 * 用户管理
 * Class User
 * @package app\api
 */
class User extends Controller
{
    /**
     * 当前用户详情
     * @return Json
     * @throws BaseException
     */
    public function info(): Json
    {
        // 当前用户信息
        $userInfo = UserService::getCurrentLoginUser(true);
        // 获取会员等级
        $userInfo['grade'];
        return $this->renderSuccess(compact('userInfo'));
    }
	
	/**
	 * 手机号绑定
	 * @return Json
	 * @throws \cores\exception\BaseException
	 */
	public function bindMobile(): Json
	{
	    $model = new UserModel;
	    if (!$model->bindMobile($this->postForm())) {
	        return $this->renderSuccess($model->getError() ?: '操作失败');
	    }
	    return $this->renderSuccess('恭喜您，手机号绑定成功');
	}

}
