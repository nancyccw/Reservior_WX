<?php
namespace app\home\model;
/**
 * Created by PhpStorm.
 * User: ccw
 * Date: 2020/10/13
 * Time: 15:06
 */

use think\Db;
use think\Model;


class readRainfall extends Model
{
    protected $table = 'WATER';

    static function sel()
    {
        $result = Db::table('WATER')->find();

        return $result;
    }

    static function selByInterval($startime,$endtime){

        $result = Db::table('WATER')->where('DT', 'between', [$startime, $endtime])->select();

        return $result[0];
    }
}