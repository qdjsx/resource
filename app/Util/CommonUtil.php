<?php namespace App\Util;

use Illuminate\Http\Request;
use App\Util\RedisUtil;

class CommonUtil {
    
    /**
     * 获取客户端ip
     */
    public static function getIp(){
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

    /**
     * 内容换行
     */
    public static function contentStrReplace($content,$tabBefore='#@#',$tabAfter='<br />'){
        if(!$content) return $content;
        $content = str_replace($tabBefore,$tabAfter,$content);
        return $content;
    }
    
    /**
     * 返回结果处理
     */
    public static function returnHandle($errorCode=0,$errorMsg='',$ret=[],$params=[]){
        $errorCodeKey = $params['codeKey'] ?? 'errorCode' ;
        $errorMsgKey = $params['msgKey'] ?? 'errorMsg' ;
        $ret[$errorCodeKey] = $errorCode;
        $ret[$errorMsgKey] = $errorMsg;
        return $ret;
    }
    
    /**
     * 数字初始化
     * @param int $decimals 要保留多少位小数 默认 2
     * @param str $decimalPoint 小数点用什么代替 默认 '.'
     * @param str $separator 万位和千位之间用什么分割 默认 空
     */
    public static function numberFormat($data,$decimals=2,$decimalPoint='.',$separator=''){
        $ret = number_format($data,$decimals,$decimalPoint,$separator) ;
        return $ret;
    }
    
    /**
     * 将剩余时间戳转换成时间格式
     */
    public static function timeRemainDate($times,$params=[]){
        if($times<=0) return "";
        $nameSecond = $params['nameSecond'] ?? '秒' ;
        $nameMinute = $params['nameMinute'] ?? '分' ;
        $nameHour = $params['nameHour'] ?? '小时' ;
        $nameDay = $params['nameDay'] ?? '天' ;
        $showSecond = $params['showSecond'] ?? true ;
        $showMinute = $params['showMinute'] ?? true ;
        $showHour = $params['showHour'] ?? true ;
        $showDay = $params['showDay'] ?? true ;
        $res = "";
        $dayTime = intval($times/86400) ;
        $dayTime && $showDay && $res .= $dayTime . $nameDay;
        $hourTime = intval(($times-$dayTime*86400)/3600) ;
        $hourTime && $showHour && $res .= $hourTime . $nameHour;
        $minuteTime = intval(($times-$dayTime*86400-$hourTime*3600)/60) ;
        $minuteTime && $showMinute && $res .= $minuteTime . $nameMinute;
        $secondTime = $times-$dayTime*86400-$hourTime*3600-$minuteTime*60 ;
        $secondTime && $showSecond && $res .= $secondTime . $nameSecond;
        return $res;
    }

    /**
     * 获取当天剩余时间戳
     * @param int $startTime 开始时间戳 默认当前时间戳
     * @param int $endTime 结束时间戳 默认当天最后时间戳
     */
    public static function getTimeDiffNow($startTime='',$endTime=''){
        $timeTodayLast = $endTime ? $endTime : strtotime(date('Y-m-d 23:59:59'));
        $timeNow = $startTime ? $startTime : time();
        $timeDiff = $timeTodayLast - $timeNow ;
        $timeDiff = $timeDiff > 0 ? $timeDiff : 0 ;
        return $timeDiff;
    }

    /**
     * 将小数分割
     */
    public static function numberDivision($number){
        $number = $number + 0 ;
        $numberArr = explode('.', $number);
        $int = $numberArr[0] ?? 0 ;
        $float = $numberArr[1] ?? '' ;
        $ret = [
            'int'   => $int ,
            'float' => $float ,
        ];
        return $ret;
    }
    
    /**
     * 去掉小数点后面的0
     */
    public static function numberDelZero($number){
        $number = rtrim(rtrim($number, '0'), '.');
        $number==0 && $number = 0 ;
        return $number;
    }
    
    /**
     * 数字初始化，并去掉小数点后面的0
     */
    public static function numberFormatDelZero($data,$decimals=2,$decimalPoint='.',$separator=''){
        $data = self::numberFormat($data, $decimals, $decimalPoint, $separator);
        $data = self::numberDelZero($data);
        return $data;
    }

}
