<?php
declare (strict_types = 1);

namespace app\anding\controller\api\auth;

use app\anding\controller\api\Base;
use app\anding\middleware\auth\Auth;

/**
 * 刷新token
 * Class RefreshToken
 * @package app\anding\controller\api\auth
 */
class RefreshToken extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $refresh_token = $this->request->post('refresh_token');
        $auth = new Auth();
        $rs = $auth->refreshToken($refresh_token);
        return json_return($rs);
    }

}
