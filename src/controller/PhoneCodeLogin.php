<?php
declare (strict_types = 1);

namespace app\anding\controller\api\auth;

use app\anding\controller\api\Base;
use app\anding\exceptions\ApiException;
use app\anding\middleware\auth\Auth;

class phoneCodeLogin extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');
        $validate = \think\facade\Validate::rule([
                'phone'  => 'require|mobile',
                'code' => 'length:4'
            ]);
        if (!$validate->batch(true)->check([
            'phone' => $phone,
            'code' => $code,
        ])) {
            ApiException::throws(ApiException::$validError, $validate->getError());
        }
        $auth = new Auth();
        $rs = $auth->loginPhoneCode($phone, $code);
        return json_return($rs);
    }

}
