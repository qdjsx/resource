<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Util\LoginUtil;
use Illuminate\Http\Request;
use App\Models\Admin;
use Session;
use Hash;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\MyBaseController;

class LoginController extends MyBaseController
{
    private $rulesArr = [
        'password' => 'required',
    ];

    public $emailArr = [
        'admin@jntinchina.com',
        'jiangshixuan@wasair.com',
        'quchuanqi@wasair.com',
    ];
    // ||  ($ip == )|| ($ip == '') || ($ip == '')
    public  $ipArr = [
        '43.224.46.76'=>1,
        '124.202.212.14'=>1,
        '36.102.228.92'=>1,
        '36.102.222.170'=>1,
        '118.144.244.137'=>1,
        '118.144.244.138' => 1,
        '118.144.244.139' => 1,
        '118.144.244.140' => 1,
        '118.144.244.141' => 1,
        '118.144.244.142' => 1,
        '118.144.244.143' => 1,
        '115.182.121.50'  => 1,
        '::1'=>1,
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->messages = [
            "required" => ":attribute不能为空",
        ];

        $this->customAttributes = [
            'email' => '邮箱',
            'password' => '密码',
        ];
    }
    public function login()
    {
        $ip = $this->getIp();
        return view('login.login')->with('ip',$ip);
    }

    public  function  sendcode()
    {
        $item = Admin::where('email', $this->params['email'])->first();
        //先验证密码，后验证短信
        if (!$item || !(Hash::check($this->params['password'], $item->password))) {
            $this->errorCode = parent::OTHER_ERROR_CODE;
            $this->msg  = '账号密码不正确';
            $this->showResult();
            exit;
        }


        if(!is_numeric($item->phone) || strlen($item->phone) != 11){
            $this->errorCode = parent::OTHER_ERROR_CODE;
            $this->msg = '手机号不对，请联系相关人员';
            $this->showResult();
            exit;
        }
        //限制发送时间
        $validKey  ='limit_phonetime_'.$item->phone;
        $validKeyValue = Redis::get($validKey);
        if ($validKeyValue) {
            //不超过两分钟
            $this->errorCode = parent::OTHER_ERROR_CODE;
            $this->msg = '短信每1分钟才能再次请求';
            $this->showResult();
            exit;
        }
        //限制发送次数
        $oneDayFrequency = 'limit_'.date('Y-m-d') . '_' . $item->phone;
        $oneDayFrequencyValue = Redis::get($oneDayFrequency);
        if ($oneDayFrequencyValue ) {
            Redis::incr($oneDayFrequency);
            if($oneDayFrequencyValue >= $item->tries_limit){
                $this->errorCode = parent::OTHER_ERROR_CODE;
                $this->msg = '小主，你今天请求已经10次了!!!';
                $this->showResult();
                exit;
            }

        }else{
            Redis::setex($oneDayFrequency,86400,1);
        }
        $code = $this->randkeys(6);
        $this->sendSms($item->phone, $code,'签到后台');
        Redis::setex($this->params['email'],60,$code);
        Redis::setex($validKey,60,1);
        $this->errorCode = parent::SUCCESS;
        $this->msg  = '验证码已发送';
        $this->showResult();

    }
    public function check(Request $request)
    {
        $this->rules = $this->rulesArr;
        $this->validateParams();

        //这里进行验证码验证。
        $a = true;
        $ip = $this->getIp();
        if(($ip == '') ||  isset($this->ipArr[$ip])) $a = false;
//        if($a &&  !in_array($this->params['email'],$this->emailArr)){
//            $phoneCode = $this->params['phone_code'];
//            if(strlen($phoneCode) != 6 || Redis::get($this->params['email']) != $phoneCode){
//                $this->errorCode = parent::OTHER_ERROR_CODE;
//                $this->msg  = '验证码不正确';
//                $this->showResult();
//                exit;
//            }
//        }
        $item = Admin::where('email', $this->params['email'])->first();
        if(!$item){
            $item = Admin::where('username', $this->params['email'])->first();
        }
        if(!$item){
            $this->errorCode = parent::OTHER_ERROR_CODE;
            $this->msg  = '账号不正确';
            $this->showResult();
            exit;
        }
        //账户状态判断
        if ($item->status != 1){
            $this->errorCode = parent::OTHER_ERROR_CODE;
            $this->msg  = '该账户禁止登陆，请联系管理人员';
            $this->showResult();
            exit;
        }

        //密码验证
        if ($item && Hash::check($this->params['password'], $item->password)) {
            $this->errorCode = parent::SUCCESS;
            Session::put('session_id',$item->id);
            Session::save();
            $this->msg  = '登录成功';
//            $log = new AdminLog();
//            $log->admin_id = $item->id;
//            $log->create_at = date('Y-m-d H:i:s');
//            $log->permission_id = -1;
//            $log->save();
        }else{
            $this->errorCode = parent::OTHER_ERROR_CODE;
            $this->msg  = '密码不对';
        }

        //菜单逻辑
        $getMenu  = Session::get('menu');
        if(!$getMenu){
            $menu = LoginUtil::MenuToSession($item->id);
            Session::put('menu', $menu);
            Session::save();
        }
        $this->showResult();
        exit;
    }

    public  function randkeys($length)
    {
        $pattern = '1234567890';
        $key = '';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern[mt_rand(0,9)];    //生成php随机数
        }
        return $key;
    }

    private function sendSms($phone, $code,$smsSign)
    {
        if (!$smsSign) $smsSign =  '懒人签到';
        $msg = "【".$smsSign."】您的验证码为:" . $code . ",该验证码1分钟内有效,请勿泄露于他人";
        $userName = "lanren";        //必选	string	帐号名称
        $userPass = "lr20171225";        //必选	string	密码
        $mobile = $phone;            //必选	string	多个手机号码之间用英文“,”分开，最大支持500个手机号码，同一请求中，最好不要出现相同的手机号码
        $subid = "";            //选填	string	通道号码末尾添加的扩展号码
        $url = 'http://101.200.29.88:8082/SendMT/SendMessage';

        $message = urlencode($msg);

        $params = 'CorpID=' . $userName . '&Pwd=' . $userPass . '&subid=' . $subid . '&Mobile=' . $mobile . '&Content=' . $message;

        return  $this->curlGet($url, $params);
    }

    private function curlGet($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }


    public function logout()
    {
        Session::flush();

        return redirect('auth/login');
    }


    /**
    *获取用户真实ip
     */
    public static function getIp()
    {
        $clientIps = [];

        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
            $clientIps[] = $_SERVER["HTTP_CLIENT_IP"];

        if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $clientIps[] = $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (!empty($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]))
            $clientIps[] = $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];

        if(!empty($_SERVER["REMOTE_ADDR"]))
            $clientIps[] = $_SERVER["REMOTE_ADDR"];

        foreach ($clientIps as $key => $clientIp) {
            if (!filter_var($clientIp, FILTER_VALIDATE_IP)) {
                unset($clientIps[$key]);
                continue;
            }

            $long = ip2long($clientIp);
            if (
                ( $long >= ip2long("10.0.0.0") && $long <= ip2long("10.255.255.255"))
                ||
                ($long >= ip2long("172.16.0.0") && $long <= ip2long("172.31.255.255"))
                ||
                ($long >= ip2long("192.168.0.0") && $long <= ip2long("192.168.255.255"))
            ) {
                unset($clientIps[$key]);
            }
        }

        $clientIps = array_merge($clientIps, []);

        return (string)($clientIps  ? $clientIps[0] : '');
    }


}
