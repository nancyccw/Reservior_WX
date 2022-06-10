<?php
namespace app\Home\controller;

use app\Common\controller\CommonController;
use app\Common\controller\JsSDK;
use app\Common\controller\Lunar;
use app\Common\controller\WeiXinAdvanced;
use app\home\model\readwater_one;
use app\home\model\ztcs;
use app\Service\model\warning;
use think\Config;
use think\Request;

class Index extends CommonController
{

    var $appID = "";
    var $appsecret = "";
    var $root = "";
    var $weiXinAdvanced = "";
    var $index = "";
    var $lunar = "";
    var $Agentld = "";

    public function _initialize()
    {
        $this->appID = config('appID');
        $this->appsecret = config('appsecret');
        $this->root = config('root');
        $this->lunar = new Lunar();
        $this->Agentld = config('Agentld');
        $this->index = new \app\Service\controller\Index();
        $this->weiXinAdvanced = new WeiXinAdvanced($this->appID, $this->appsecret);
    }

    public function index(){

        $info = array(
            'info' => '欢迎关注深圳市腾龙信息技术有限公司微信号!',
            'root' => $this->root
        );

        $this->assign('info',$info);
        return $this->fetch('index');
    }

    //获取部门列表信息
    public function test(){
        $result = $this->weiXinAdvanced->getDeptList();
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    //进入信息群发登录页
    public function login(){
        $data = array(
            'root' => $this->root,
            'appid'=>$this->appID,
            'Agentld' => $this->Agentld
        );
        $this->assign('data',$data);
        return $this->fetch('public/login');
    }

    //上传素材
    public function UploadMaterial(){
        if(!$_GET['code']){
            return $this->fetch('error');
        }
        $flag = false;
        //获取中心领导部门id
//        $dept_id = config('dept_id');
        $data = $this->getUserInfo($_GET['code']);
        //获取访问用户的部门列表
//        $user_dept_array = $this->weiXinAdvanced->get_QiYeuser_info($data['openid']);
//        foreach($user_dept_array as $item => $value){
//            //如果用户属于中心领导部门，则进入UploadMaterial页面
//            if($value == $dept_id){
//                $flag = true;
//            }
//        }
//        if(!$flag){
//            //提示没有权限
//            return $this->fetch('NotAuthority');
//        }
        //获取全部胡部门列表
        $dept_list = $this->weiXinAdvanced->getDeptList();
//        $dept_json = json_decode($dept_list,true);
        $dept_array = array();

        foreach($dept_list['department'] as $item => $value){
            $dept_array[$value['name']] = $value;
        }
//        print_r($dept_array);
        $this->assign('dept_list',$dept_array);
        $this->assign('data',$data);
        return $this->fetch('UploadMaterial');
    }

    //获取用户信息方法
    public function getUserInfo($code){

        $JsSDK = new JsSDK($this->appID, $this->appsecret);
        $data = $JsSDK->getSignPackage();
        $userInfo = $this->weiXinAdvanced->get_user_info_ext($code);

        $data['openid'] = $userInfo['openid'];
        $data['nickname'] = $userInfo['nickname'];
        $data['ak'] = config('ak');
        $data['appId'] =$this->appID;
        $data['root'] = config('root');
        $data['serviceAPI'] = config('serviceAPI');

        return $data;

    }
    /**
     * 测试连接内网数据库
     */
    public function  getReadWaterData(){
        $readWaterOne = new readwater_one();
        $result =  $readWaterOne->limit(0,100)->order('timestamp','desc')->select();
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    public function getRainfall(){

        $today = new \DateTime();

        $endTime = $today->format("Y-m-d H:i:s.u");
        $today->setTime(8,0,0);
        $startTime = $today->format("Y-m-d H:i:s.u");
        $accumRainfall = $this->index->getAccumRainfall();

        $data = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'bz_name' => "test",
            'root' => config('root')
        );
        $this->assign('data', $data);
        $this->assign('accumRainfall',$accumRainfall);
        return $this->fetch('rainfallChart');
    }

    public function getReservoirWater(){

        $waterList = $this->index->getReserviorWater();
        $info = array(
            'root' => $this->root,
            'bz_type' => "水库"
        );

        $this->assign('info',$info);
        $this->assign('list',$waterList);
        return $this->fetch('riverChart');
    }

    public function getDamSaturationLine (){
//        $damList = $this->index->getDamSaturationLine();

        $designFL = config('DamParam.designFL');
        $damHeight = config('DamParam.damHeight');
        $damWidth = config('DamParam.damWidth');
        $damProfile = config('DamParam.profile');
        $piezometer = config('DamParam.piezometer_fracture');
//        $piezometer2 = config('DamParam.piezometer_fracture2');
//        $piezometer3 = config('DamParam.piezometer_fracture3');
//        $waterLevel =  $this->index->getLatestWL();

        $damParam = array(
            'designFL'=> round($designFL,2),
            'damHeight'=> round($damHeight,2),
            'damWidth'=> round($damWidth,2),
//            'waterLevel'=> round($waterLevel,2),
            'damProfile'=>
                array(
                    'x0'=> round($damProfile["x0"],2),
                    'y0'=> round($damProfile["y0"],2),
                    'x1'=> round($damProfile["x1"],2),
                    'y1'=> round($damProfile["y1"],2),
                    'x2'=> round($damProfile["x2"],2),
                    'y2'=> round($damProfile["y2"],2),
                    'x3'=> round($damProfile["x3"],2),
                    'y3'=> round($damProfile["y3"],2),
                    'x4'=> round($damProfile["x4"],2),
                    'y4'=> round($damProfile["y4"],2),
                    'x5'=> round($damProfile["x5"],2),
                    'y5'=> round($damProfile["y5"],2),
                    'x6'=> round($damProfile["x6"],2),
                    'y6'=> round($damProfile["y6"],2),
                    'x7'=> round($damProfile["x7"],2),
                    'y7'=> round($damProfile["y7"],2),
                    'x8'=> round($damProfile["x8"],2),
                    'y8'=> round($damProfile["y8"],2)
                    )
        );

        $piezometerParam = array(
                'p1_left' => round($piezometer["p1_left"],2),
                'p1_bottom' => round($piezometer["p1_bottom"],2),
                'p1_right' => round($piezometer["p1_right"],2),
                'p1_top' => round($piezometer["p1_top"],2),
                'p2_left' => round($piezometer["p2_left"],2),
                'p2_bottom' => round($piezometer["p2_bottom"],2),
                'p2_right' => round($piezometer["p2_right"],2),
                'p2_top' => round($piezometer["p2_top"],2),
                'p3_left' => round($piezometer["p3_left"],2),
                'p3_bottom' => round($piezometer["p3_bottom"],2),
                'p3_right' => round($piezometer["p3_right"],2),
                'p3_top' => round($piezometer["p3_top"],2),
                'p4_left' => round($piezometer["p4_left"],2),
                'p4_bottom' => round($piezometer["p4_bottom"],2),
                'p4_right' => round($piezometer["p4_right"],2),
                'p4_top' => round($piezometer["p4_top"],2)
        );
        $info = array(
            'root' => $this->root,
            'bz_type' => "水库"
        );

        $this->assign('info',$info);
        $this->assign('piezometerParam',$piezometerParam);
        $this->assign('damParam',$damParam);
//        $this->assign('list',$damList);
        return $this->fetch('reserviorDamChart');
    }
    public function getSluiceGateStatus(){

        $ztcs_list = $this->index->getNewBZInfo();
        $info = array(
            'root' => $this->root,
            'bz_type' => "闸门"
        );
        $this->assign('info',$info);
        $this->assign('list',$ztcs_list);
        return $this->fetch('sluiceGate');
    }

    public function testfunc(){

        $info = array(
            'root' => $this->root,
            'bz_type' => "测试"
        );

        $this->assign('info',$info);
        return $this->fetch('bootstrap');
    }
}

