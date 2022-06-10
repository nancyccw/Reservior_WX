<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 14:38
 */
namespace app\Common\controller;
class DataHelper {
    public static function GetData($method, $destination, $body){
        $serializer = new \Amfphp_Core_Amf_Serializer();
        //$serializer->voConverter = $this->voConverter;
        $data = new \Amfphp_Core_Amf_Packet();
        $data->amfVersion = 3;
        $message = new \Amfphp_Core_Amf_Message();
        $message->targetUri = 'null';
        $message->responseUri = '/2';
        $message->reserved = 320;
        $message->data = new \AmfphpFlexMessaging_RemotingMessage($method, '00000000-0000-0000-0000-000000000000', null, $destination);
        $message->data->body = $body;
        $message->data->headers->DSEndpoint=null;
        $message->data->headers->DSId	='00000000-0000-0000-0000-000000000000';
        $data->messages[] = $message;
        $result = $serializer->serialize($data);
        $rawPostData = self::https_request('http://61.144.121.35:8888/messagebroker/amf', $result);

        $deserializer = new \Amfphp_Core_Amf_Deserializer();
        $response = $deserializer->deserialize(null, null, $rawPostData);
        $content = $response->messages[0]->data->body;
        return $content;

        /*
        $result = bin2hex($rawPostData);
        $tmp = '';
        for ($x=0; $x<=strlen($result); $x=$x+32) {
            for ($y=0; $y<16; $y++) {
                $tmp = $tmp . substr($result, $x + ($y * 2), 2) . " ";
            }
          $tmp = $tmp . "<br>";
        }
        echo $tmp . $rawPostData;
        */
    }

        public static function getData1(){


        }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    public static function  https_request($url = '', $param = '') {
        if (empty($url)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if (!empty($curlPost)){
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        }
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    function stringToByteArray($str,$charset) {
        $str = iconv($charset,'UTF-16',$str);
        preg_match_all('/(.)/s',$str,$bytes);  //注：本文的盗版已经有了。不过，提示一下读者，这里的正则改了。
        $bytes=array_map('ord',$bytes[1]) ;
        return $bytes;
    }

    function byteArrayToString($bytes,$charset) {
        $bytes=array_map('chr',$bytes);
        $str=implode('',$bytes);
        $str = iconv('UTF-16',$charset,$str);
        return $str;
    }
}