<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/4/26 0026 16:26
 * for: 短信SDK
 */

namespace dh2y\sms;


use think\Config;


/** .-----------------------------使用说明---------------------------------
$sms = new Sms();
$sms->sendSms('17xx11076xx','你好，dh2y先生。我发给短信测试一下啦');//普通短信


$sms->sendSmsCode('17xx11076xx','register');                 //验证码短信
$sms->verifySmsCode('17xx11076xx',587620);                   //验证短信的验证码
echo $sms->getError();             //获取失败错误信息(验证，发送等错误信息)
-------------------------------------------------------------------*/

class Sms
{
    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    protected $handler    ;

    protected $config = [];

    protected $scene;      //验证码使用场景

    protected $message;

    protected $test = false;

    private static $instance=null;  //创建静态单列对象变量

    /**
     * Sms constructor.
     * @param array $config
     * @throws \Exception
     */
    private function __construct($config = array()){
        if(empty( $config )&& $C = Config::get('sms')){
            $config = $C['SMS_SDK'];
            $this->scene = $C['SMS_SCENE'];
            $this->test = $C['IS_TEST'];
        }
        /* 获取配置 */
        $this->config   =   array_merge($this->config, $config);
        $class = '\\dh2y\\sms\\service\\'. ucfirst($this->config['class']);
        if(class_exists($class)){
            $this->handler  =   new $class($this->config);
        }else{
            // 类没有定义
            throw new \Exception('没有这个短信服务商');
        }
    }

    /**
     * 单列模式
     * @param array $config
     * @return Sms|null
     */
    public static function getInstance($config = array()){
        if(empty(self::$instance)) {
            self::$instance=new Sms($config);
        }
        return self::$instance;
    }

    /**
     * 克隆函数私有化，防止外部克隆对象
     * @throws \Exception
     */
    private function __clone(){
        throw new \Exception('禁止克隆');
    }

    /**
     * 返还错误信息
     * @return string
     */
    public function getError(){
        return $this->handler->getError();
    }

    /**
     * 设置错误信息
     * @param $error
     */
    public function setError($error){
        $this->handler->setError($error);
    }

    /**
     * 发送验证码
     * @param string $phone 手机号
     * @param string $scene 短信验证码场景
     * @param \Closure $function
     * @param string $code
     * @return bool
     */
    public function sendSmsCode($phone,$scene,\Closure $function=null,$code=''){
        if($this->test){
            if($function!=null){
                $function($phone,'111111');
            }else{
                session('sms_phone',$phone);
                session('sms_code','111111');
                session('sms_expire',time()+1800);
                session('sms_time',time()+60);
            }
            //设置验证码发送场景
            $code ='11111';
            $message = str_replace(['%code%'], [$code],$this->scene[$scene]);
            $message = $message.$this->config['signature'];
            $this->message = $message;
            return true;
        }
        if(!isset($code)||$code==''){
            $code = rand('111111','999999');
        }
        //设置验证码发送场景
        $message = str_replace(['%code%'], [$code],$this->scene[$scene]);

        //签名不存在加上签名
        if(!strstr($message,$this->config['signature'])){
            $message = $message.$this->config['signature'];
        }

        $this->message = $message;
        if($this->handler->sendSms($phone, $message)){
            if($function!=null){
                $function($phone,$code);
            }else{
                session('sms_phone',$phone);
                session('sms_code',$code);
                session('sms_expire',time()+300);
                session('sms_time',time()+60);
            }
            return true;
        }else{
            $this->setError('短信发送失败！');
            return false;
        }
    }

    /**
     * 获取发送的短信消息内容
     * @return mixed
     */
    public function getMessage(){
        return $this->message;
    }

    /**
     * 验证短信验证码
     * @param $phone
     * @param $code
     * @param bool $clean 验证完清理session
     * @return bool  false验证失败  true验证成功   失败原因通过getError()获取
     */
    public function verifySmsCode($phone,$code,$clean = true){
        if(!$code || !$phone){
            $this->setError('验证码或手机不存在！');
            return false;
        }

        if(session('sms_time')>time()){
            $this->setError('短信发太快了！');
        }

        if(session('sms_expire')<time()){
            session('sms_phone',null);
            session('sms_code',null);
            $this->setError('验证码已失效,稍后再试！');
            return false;
        }

        if(session('sms_phone')!=$phone){
            $this->setError('短信验证码手机错误！');
            return false;
        }


        //验证码通过
        if(session('sms_phone')==$phone&&session('sms_code') == $code){
            if($clean){
                session('sms_phone',null);
                session('sms_code',null);
                session('sms_expire',null);
            }
            return true;
        }

        $this->setError('验证码错误！');
        return false;
    }

    /**
     * 发送短信
     * @param string $phone 手机号
     * @param string $scene 场景
     * @param array $param 替换的参数
     * @return bool
     */
    public function sendSms($phone, $scene,$param = []){
        //发送内容
        if(!empty($param) && is_array($param)){
            $one = [];$two = [];
            foreach ($param as $key=>$value){
                $one[] = '%'.$key.'%';
                $two[] = $value;
            }
            $message = str_replace($one, $two,$this->scene[$scene]);
            $message = '【'.$this->config['signature'].'】'.$message;
            $this->message = $message;
        }else{
            $this->message = $this->scene[$scene];
        }
        //是否是测试发送
        if($this->test){
            return true;
        }
        if($this->handler->sendSms($phone, $this->message)){
            return true;
        }else{
            $this->setError('短信发送失败！');
            return false;
        }
    }

    public function __call($method,$args){
        //调用缓存类型自己的方法
        if(method_exists($this->handler, $method)){
            return call_user_func_array(array($this->handler,$method), $args);
        }else{
            // 类没有定义
            throw new \Exception('没有这个短信服务商驱动方法:'.$method);
        }
    }
}