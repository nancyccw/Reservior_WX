<?php
namespace app\home\model;
use think\Db;
use think\Model;


class ReserviorDamModel extends Model
{
    protected $table = 'SAFETY';

    static function sel()
    {
        $result = Db::table('SAFETY')->find();

        return $result;
    }

    static function selByInterval($startime,$endtime){

        $result = Db::table('SAFETY')->where('DT', 'between', [$startime, $endtime])->select();

        return $result[0];
    }
}