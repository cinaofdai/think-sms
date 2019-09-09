<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/4/26 0026 16:26
 *  for: 短信抽象类
 *  各种短信服务商的接口开发都要实现此抽象类
 */

namespace dh2y\sms\service;


use think\facade\Config;

abstract class MessageInterface
{

    /**
     * 默认配置
     * @var array
     */
    protected $config = [
        'account' => '',                    //  短信账号
        'password'=> ''                     //  短信账号密码
    ];

    private $error;  //错误信息

    /**
     * 构造函数
     * MessageInterface constructor.
     * @param array $config 短信配置
     */
    public function __construct($config = array()){
        if(empty( $config )&& $C = Config::get('sms.')){
            $this->config = $C['SMS_SDK'];
        }
        /* 获取配置 */
        $this->config   =   array_merge($this->config, $config);
    }


    /**
     * 使用 $this->name 获取配置
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name) {
        return $this->config[$name];
    }

    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    public function __isset($name){
        return isset($this->config[$name]);
    }

    /**
     * 获取短信异常信息
     * @return mixed
     */
     public function getError(){
       return $this->error;
     }

    /**
     * 设置短信异常信息
     * @param $message
     */
     public function setError($message){
         $this->error = $message;
     }

    /**
     * 发送短信
     * @param $phone
     * @param $message
     * @return mixed
     */
    abstract public function sendSms($phone,$message);

    /**
     * 获取短信请求地址
     * @param $api
     * @return mixed
     */
    abstract public function getRequestUrl($api);

}