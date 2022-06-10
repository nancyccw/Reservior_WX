<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/21
 * Time: 19:21
 * 控制定时发送警告任务的php
 */
    $home_index = new \app\Home\controller\Index();
    $service_index = new \app\Service\controller\Index();
    $list = $home_index->getBZData("");
    $service_index->sendWarning($list);

?>