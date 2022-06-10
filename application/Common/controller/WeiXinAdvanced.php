<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 14:42
 */
namespace app\Common\controller;


class WeiXinAdvanced
{
    var $Agentld = "";
    var $appid = "";
    var $appsecret = "";
    var $isQy = false;
    var $root = "";

    //构造函数，获取Access Token
    public function __construct($appid = NULL, $appsecret = NULL)
    {
        if($appid){
            $this->appid = $appid;
        }
        if($appsecret){
            $this->appsecret = $appsecret;
        }
        $this->root = config('root');
        $this->Agentld = config('Agentld');
        $this->isQy = config('isQy');
        $JsSDK = new JsSDK($this->appid, $this->appsecret);
        $this->access_token = $JsSDK->getAccessToken();
        /*
        //hardcode
        $this->lasttime = 1395049256;
        $this->access_token = "nRZvVpDU7LxcSi7GnG2LrUcmKbAECzRf0NyDBwKlng4nMPf88d34pkzdNcvhqm4clidLGAS18cN1RTSK60p49zIZY4aO13sF-eqsCs0xjlbad-lKVskk8T7gALQ5dIrgXbQQ_TAesSasjJ210vIqTQ";

        if (time() > ($this->lasttime + 7200)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $res = $this->https_request($url);
            $result = json_decode($res, true);
            //save to Database or Memcache
            $this->access_token = $result["access_token"];
            $this->lasttime = time();
        }
        */
    }

    //获取关注者列表
    public function get_user_list($next_openid = NULL)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token."&next_openid=".$next_openid;
        $res = $this->https_request($url);
        return json_decode($res, true);
    }

    //获取用户基本信息
    public function get_user_info($openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->https_request($url);
        return json_decode($res, true);
    }

    //获取企业成员信息
    public function get_QiYeuser_info($openid)
    {
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token."&userid=".$openid."&lang=zh_CN";
        $res = $this->https_request($url);
        $result_array = json_decode($res, true);
        //获取部门id列表
        $department_array = $result_array['department'];
        return $department_array;
    }

    //获取用户基本信息
    public function get_user_info_ext($code)
    {
        $openid = '';
        if($this->isQy){
            $access_token_url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$this->access_token&code=$code";
            $access_token_json = $this->https_request($access_token_url);
            $access_token_array = json_decode($access_token_json, true);
            // userid转换成openid接口
            /*
            $access_token_url = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=$this->access_token";
            $data = '{"userid": "'.$access_token_array["UserId"].'" }';
            $access_token_json = $this->https_request($access_token_url, $data);
            $access_token_array = json_decode($access_token_json, true);
            */
            $openid = $access_token_array['UserId'];
            return json_decode('{ "openid": "'.$openid.'", "nickname": "'.$openid.'" }', true);
        }
        else {
            //oauth2的方式获得openid
            $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appsecret&code=$code&grant_type=authorization_code";
            $access_token_json = $this->https_request($access_token_url);
            $access_token_array = json_decode($access_token_json, true);
            $openid = $access_token_array['openid'];
            return $this->get_user_info($openid);
        }

        //非oauth2的方式获得全局access token
        //$new_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        //$new_access_token_json = $this->httpGet($new_access_token_url);
        //$new_access_token_array = json_decode($new_access_token_json, true);
        //$new_access_token = $new_access_token_array['access_token'];

        //全局access token获得用户基本信息
        //$userinfo_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$new_access_token&openid=$openid";
        //$userinfo_json = $this->httpGet($userinfo_url);
        //$userinfo_array = json_decode($userinfo_json, true);
        //return $userinfo_array;
    }

    //创建菜单
    public function create_menu($data)
    {
        $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token."&agentid=".$this->Agentld;
//        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
        $res = $this->https_request($url, $data);
        return json_decode($res, true);
    }
    //删除菜单
    public function delete_menu(){
        $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->access_token."&agentid=".$this->Agentld;
        $res = $this->https_request($url);
        return json_decode($res, true);
    }

    //获取部门列表
    public function getDeptList(){
        $url = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=".$this->access_token."&id=";
        $res = $this->https_request($url);
        return json_decode($res, true);
    }

    //创建个性化菜单
    public function addconditional($data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=".$this->access_token;
        $res = $this->https_request($url, $data);
        return json_decode($res, true);
    }

    //获取用户标签用于个性化菜单
    public function getTag(){
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$this->access_token;
        $res = $this->https_request($url);
        return json_decode($res, true);
    }



    public function send_custom_image($data)
    {

        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->access_token;
        return $this->https_request($url, urldecode(json_encode($data)));
    }


    //生成参数二维码
    public function create_qrcode($scene_type, $scene_id)
    {
        switch($scene_type)
        {
            case 'QR_LIMIT_SCENE': //永久
                $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
                break;
            case 'QR_SCENE':       //临时
                $data = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
                break;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;
        $res = $this->https_request($url, $data);
        $result = json_decode($res, true);
        return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($result["ticket"]);
    }

    //创建分组
    public function create_group($name)
    {
        $data = '{"group": {"name": "'.$name.'"}}';
        $url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=".$this->access_token;
        $res = $this->https_request($url, $data);
        return json_decode($res, true);
    }

    //移动用户分组
    public function update_group($openid, $to_groupid)
    {
        $data = '{"openid":"'.$openid.'","to_groupid":'.$to_groupid.'}';
        $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$this->access_token;
        $res = $this->https_request($url, $data);
        return json_decode($res, true);
    }

    //上传多媒体文件
    public function upload_media($type, $file)
    {
        $data = array("media"  => "@".dirname(__FILE__).'\\'.$file);
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->access_token."&type=".$type;
        $res = $this->https_request($url, $data);
        return json_decode($res, true);
    }

    //上传素材
    public function upload_Material($type,$file_info){
        $url = "https://qyapi.weixin.qq.com/cgi-bin/media/upload?type=".$type."&access_token=".$this->access_token;
        $output=$this->https_request($url, $file_info);
        return $output;
    }

    //下载多媒体文件
    public function download_media($media_id)
    {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->access_token."&media_id=".$media_id;
        if($this->isQy){
            $url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=".$this->access_token."&media_id=".$media_id;
        }
        $res = $this->https_request($url,null,true);
        return $res;
    }

    //地理位置逆解析
    public function location_geocoder($latitude, $longitude)
    {
        $url = "http://api.map.baidu.com/geocoder/v2/?ak=B944e1fce373e33ea4627f95f54f2ef9&location=".$latitude.",".$longitude."&coordtype=gcj02ll&output=json";
        $res = $this->https_request($url);
        $result = json_decode($res, true);
        return $result["result"]["addressComponent"];
    }

    //https请求（支持GET和POST）
    protected function https_request($url, $data = null, $downloadFile = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if($downloadFile){
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_NOBODY, 0);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}