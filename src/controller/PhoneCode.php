<?php
declare (strict_types = 1);

namespace app\anding\controller\api\auth;

use app\anding\controller\api\Base;
use app\anding\exceptions\ApiException;
use app\anding\middleware\auth\Auth;
use app\third\model\ThirdPhoneCode;

/**
 * 发送手机验证码
 * Class phoneCodeLogin
 * @package app\anding\controller\api\auth
 */
class PhoneCode extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $phone = $this->request->post('phone');
        $type = $this->request->post('scene', 'login');
        $validate = \think\facade\Validate::rule([
                'phone'  => 'require|mobile'
            ]);
        if (!$validate->batch(true)->check([
            'phone' => $phone,
        ])) {
            ApiException::throws(ApiException::$validError, $validate->getError());
        }

        try {
            $rs = \app\third\logic\phoneCode\PhoneCode::ali()->sendCode(
                $type,
                $phone,
                $this->request->ip()
            );
        } catch (\Exception $e) {
            ApiException::throws([
                ApiException::$smsSendError[0],
                $e->getMessage()
            ]);
        }
        return $this->handleResponse(
            $rs,
            '发送成功',
            ApiException::$smsSendError[1],
            ApiException::$smsSendError[0]
        );
    }

}
