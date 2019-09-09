<?php
/**
 * Created by dailinlin.
 * Date: 2019/6/22 22:38
 * for:
 */


namespace dh2y\sms\service;


/** .-----------------------------配置说明---------------------------------
 * |    只需要配置 account(短信宝)和  password(短信宝)
 * |------------------------------配置方法---------------------------------
 * |   'SMS_SDK' => array(
 * |        'class' => 'Smsbao',
 * |        'account' => 'demo',
 * |        'password'=> '12345',
 * |        'signature' => '【财神通】'   
 * |   )
 * |   new Sms(config('SMS_SDK'))
 * '-------------------------------------------------------------------*/


class Smsbao extends MessageInterface
{

    protected $baseUrl = 'http://api.smsbao.com/';

    protected $statusStr = [
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    ];

    /**
     * 发送短信
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function sendSms($phone, $message)
    {

        $params = [
            "u"=>$this->account,
            "p"=>md5($this->password),
            "m"=>$phone,
            "c"=>$message
        ];

        $sendurl = $this->getRequestUrl('sms').http_build_query($params);
        $result =file_get_contents($sendurl) ;
        if ($result==0){
            return true;
        }else{
            $this->setError($this->statusStr[$result]);
            return false;
        }
    }

    /**
     * 获取短信请求地址
     * @param $api
     * @return mixed
     */
    public function getRequestUrl($api)
    {
        return $this->baseUrl.$api.'?';
    }
}