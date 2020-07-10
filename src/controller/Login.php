<?php
declare (strict_types = 1);

namespace app\anding\controller\api\auth;

use app\anding\controller\api\Base;
use app\anding\exceptions\ApiException;
use app\anding\middleware\auth\Auth;

class login extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $phone = $this->request->post('phone');
        $password = $this->request->post('password');
        $validate = \think\facade\Validate::rule([
                'phone'  => 'require|mobile',
                'password' => 'length:4,30'
            ]);
        if (!$validate->batch(true)->check([
            'phone' => $phone,
            'password' => $password,
        ])) {
            ApiException::throws(ApiException::$validError, $validate->getError());
        }
        $auth = new Auth();
        $rs = $auth->login($phone, $password);
        return json_return($rs);
    }

}
