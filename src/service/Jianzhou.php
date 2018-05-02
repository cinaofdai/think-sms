<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/4/26 0026 16:26
 * for: 上海建周短信服务商
 * website: http://www.shjianzhou.com
 */

namespace dh2y\sms\service;


/** .-----------------------------配置说明---------------------------------
 * |    只需要配置 account(上海建周账号)和  password(上海建周密码)
 * |------------------------------配置方法---------------------------------
 * |   'SMS_SDK' => array(
 * |        'class' => 'Jianzhou',
 * |        'account' => 'demo',
 * |        'password'=> '12345',
 * |   )
 * |   new Sms(config('SMS_SDK'))
 * '-------------------------------------------------------------------*/


class Jianzhou extends MessageInterface
{

    protected $baseUrl = 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/';

    /**
     * 发送短信
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function sendSms($phone, $message)
    {
        $ch = curl_init();
        $post_data = array(
            "account" => $this->account,
            "password" => $this->password,
            "destmobile" => $phone,
            "msgText" => $message,
            "sendDateTime" => "",
        );
        $post_data = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type:application/x-www-form-urlencoded;charset=utf-8;',
                'content' => $post_data
            ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
        curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl('sendBatchMessage'));
        $file_contents=curl_exec($ch);
        curl_close($ch);
        ob_clean();
        return $file_contents>0?true:false;
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