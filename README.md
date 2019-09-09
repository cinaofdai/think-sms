# think-sms
The ThinkPHP5 sms
短信通用SDK
## 安装

### 一、执行命令安装
```
composer require dh2y/think-sms
```

或者

### 二、require安装

  适配5.0
```
"require": {
        "dh2y/think-sms":"1.*"
},
```

  适配5.1
```
"require": {
        "dh2y/think-sms":"2.*"
},
```

或者
###  三、autoload psr-4标准安装
```
   a) 进入vendor/dh2y目录 (没有dh2y目录 mkdir dh2y)
   b) git clone 
   c) 修改 git clone下来的项目名称为think-sms
   d) 添加下面配置
   "autoload": {
        "psr-4": {
            "dh2y\\sms\\": "vendor/dh2y/think-sms/src"
        }
    },
    e) php composer.phar update
```


## 使用
#### 添加配置文件
```
return [
	'IS_TEST' => true,             //是否测试默认验证码 111111
    //短信基本配置
    'SMS_SDK' =>[
        'class' => 'Jianzhou',     //服务商
        'account' => 'sdk_dh2y',   //服务商账户(这里的key值可以根据服务商而定不一定是account)
        'password'=> 'dh2y',       //服务商密码(这里的key值可以根据服务商而定不一定是password)
        'signature' => '【XXXX】'   //签名
    ],

    //验证码使用场景文案
    'SMS_SCENE' =>[
        'register' => '注册验证码：%code%，有效时间5分钟，为保护您的账号安全，验证短信请勿泄露给其他人。',
        'retrieve' => '找回密码验证码：%code%，有效时间5分钟，为保护您的账号安全，验证短信请勿泄露给其他人。',
        'changePhone' =>'修改手机验证码：%code%，有效时间1分钟，为保护您的账号安全，验证短信请勿泄露给其他人。',
        'common' => '您的验证码是：%code%，有效时间5分钟，为保护您的账号安全，验证短信请勿泄露给他人。'  //普通短信验证码场景
    ]
];
```

#### 使用方法
```
$sms = Sms::getInstance();

//注册短信验证码
$result = $sms->sendSmsCode($phone,'register'); 
  
//找回密码证码
$result = $sms->sendSmsCode($phone,'retrieve');   

//普通短信验证码
$result = $sms->sendSmsCode($phone,'common',null,rand('111111','999999'));   

//更改手机号码
$result = Sms::getInstance()->sendSmsCode($phone,'changePhone',function ($p,$c){
            session('phone', $p);
            session('resms', $c);
            session('smsexpire', time() + 60);
            session('smstime', time() + 60);
        });

//验证短信验证码
$sms->verifySmsCode('17xx11076xx',587620);

```
#### 添加场景
  在配置SMS_SCENE里面添加一个test场景
```
    'SMS_SCENE' =>[
            'register' => '注册验证码：%code%，有效时间5分钟，为保护您的账号安全，验证短信请勿泄露给其他人。',
            'retrieve' => '找回密码验证码：%code%，有效时间5分钟，为保护您的账号安全，验证短信请勿泄露给其他人。',
            'changePhone' =>'修改手机验证码：%code%，有效时间1分钟，为保护您的账号安全，验证短信请勿泄露给其他人。',
            'common' => '您的验证码是：%code%，有效时间5分钟，为保护您的账号安全，验证短信请勿泄露给他人。',  //普通短信验证码场景
            'test' => '测试场景哦！测试验证码是：%code%，哈哈，就是这么简单'
        ]
```

#### 添加新的短信服务商
     
     1、在think-sms/src/service/ 新增短信服务商类 Dh2y（列如短信服务商为：dh2y）
     2、Dh2y类继承 MessageInterface 短信接口实现里面的方法（其实是抽象类）
     3、实现里面的sendSms 和 getRequestUrl方法

