<?php
declare (strict_types=1);

namespace app\anding\model;

use think\Model;

/**
 * @mixin Model
 * @property int login_fail
 * @property int id
 * @property mixed|string refresh_token
 * @property int refresh_token_time
 */
class ApiUser extends Model
{

    public function refreshTokenValid()
    {
        //一个月内有效
        return $this->refresh_token && $this->refresh_token_time && ($this->refresh_token_time > (time() - 86400 * 30));
    }

}