<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12
 * Time: 16:49
 */

namespace app\home\model;


use think\Model;

class readwater_one extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'readwater';

    protected $connection = [
        // 数据库类型
        'type'        => 'sqlsrv',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => '192.168.90.9',
        // 数据库名
        'database'    => 'scada',
        // 数据库用户名
        'username'    => 'sa',
        // 数据库密码
        'password'    => 'FSdc09',
        // 数据库连接端口
        'hostport'    => '1433',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => '',
    ];

}