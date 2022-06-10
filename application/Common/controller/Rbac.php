<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 14:41
 */
namespace app\Common\controller;
class Rbac
{
// 认证方法
    static public function authenticate($map, $model = '')
    {
        if (empty($model)) $model = config('USER_AUTH_MODEL');
        //使用给定的Map进行认证
        return model($model)->where($map)->find();
    }

//用于检测用户权限的方法,并保存到Session中
    static function saveAccessList($authId = null)
    {
        session(config('USER_AUTH_KEY'), $authId);
        return;
    }

// 登录检查
    static public function checkLogin()
    {
        //检查认证识别号
        if (!session(config('USER_AUTH_KEY'))) {
            if (config('GUEST_AUTH_ON')) {
                // 开启游客授权访问
                if (!session('_ACCESS_LIST'))
                    // 保存游客权限
                    self::saveAccessList(config('GUEST_AUTH_ID'));
            } else {
                $returnUrl = $_SERVER['PHP_SELF'];
                if( $_SERVER['QUERY_STRING'] != '') {
                    $returnUrl = $returnUrl . '?' . $_SERVER['QUERY_STRING'];
                }
                // 禁止游客访问跳转到认证网关

                redirect('http://localhost'.config('USER_AUTH_GATEWAY') . '?returnUrl='. urlencode($returnUrl));
            }
        }
        return true;
    }
}