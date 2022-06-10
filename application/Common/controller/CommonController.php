<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 14:36
 */
namespace app\Common\controller;
use think\Controller;

class CommonController extends Controller
{
    var $currentInfo;
    function _initialize()
    {
        Rbac::checkLogin();
        $this->currentInfo = array(
            'loginId' => session(config('USER_AUTH_KEY'))
        );
    }


}
