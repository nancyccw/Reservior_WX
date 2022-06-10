<?php
namespace app\home\model;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 9:17
 */
use think\Db;
use think\Model;

class readwater extends Model{

    // 设置当前模型对应的完整数据表名称
//    protected $table = 'ST_RSVR_R';
//
//    static function selByInterval($startime,$endtime){
//
//        $result = Db::table('ST_RSVR_R')->where('TM', 'between', [$startime, $endtime])->select();
//
//        return $result[0];
//    }
//    static function sel(){
//        $result = Db::table('ST_RSVR_R')->find();
//
//        return $result;
//    }

    protected $table = 'WATER';

    static function selByInterval($startime,$endtime){

        $result = Db::table('WATER')->where('DT', 'between', [$startime, $endtime])->select();

        return $result[0];
    }
    static function sel(){
        $result = Db::table('WATER')->find();

        return $result;
    }
}
?>