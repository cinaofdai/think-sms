<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/4/26 0026 16:26
 * for: 中国建网短信服务商
 * website: http://www.smschinese.cn/
 */

namespace dh2y\sms\service;


/** .-----------------------------配置说明---------------------------------
 * |    只需要配置 account(中国建网)和  password(中国建网密码)
 * |------------------------------配置方法---------------------------------
 * |   'SMS_SDK' => array(
 * |        'class' => 'Smschinese',
 * |        'account' => 'demo',
 * |        'password'=> '12345',
 * |   )
 * |   new Sms(config('SMS_SDK'))
 * '-------------------------------------------------------------------*/


class Smschinese extends MessageInterface
{

    protected $baseUrl = 'http://utf8.api.smschinese.cn/?';

    /**
     * 发送短信
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function sendSms($phone, $message)
    {
        $post_data = array(
            "Uid" => $this->account,
            "Key" => $this->password,
            "smsMob" => $phone,
            "smsText" => $message,
        );
        $api=http_build_query($post_data);
        $url = $this->getRequestUrl($api);
        $ret = file_get_contents($url);
        return $ret>=1?true:false;
    }


    /**
     * 获取短信请求地址
     * @param $api
     * @return mixed
     */
    public function getRequestUrl($api)
    {
        return $this->baseUrl.$api;
    }
  

}