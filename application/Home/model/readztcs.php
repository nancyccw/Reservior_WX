<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 14:48
 */

namespace app\home\model;


use think\Db;
use think\Model;

class readztcs extends Model
{

    // 设置当前模型对应的完整数据表名称
//    protected $table = 'readztcs';
//
//    //find只取最近一条记录
//    static function sel()
//    {
//            $result = Db::table('readztcs')->order('timestamp desc')->find();
//            return $result;
//    }
//    static function  selAllBzName()
//    {
//        $result = Db::table('readztcs')->distinct(true)->field('bzName')->select();
//        return $result;
//    }
//
//    static function selByBzName($bzName)
//    {
//        $result = Db::table('readztcs')->where('bzName',$bzName)->order('timestamp desc')->find();
//        return $result;
//    }

    protected $table = 'GATE';

    //find只取最近一条记录
    static function sel()
    {
        $result = Db::table('GATE')->order('DT desc')->find();
        return $result;
    }
    static function  selAllBzName()
    {
        $result = Db::table('GATE')->distinct(true)->field('ZM_ID')->select();
        return $result;
    }

    static function selByBzName($bzName)
    {
        $result = Db::table('GATE')->where('ZM_ID',$bzName)->order('DT desc')->find();
        return $result;
    }
}