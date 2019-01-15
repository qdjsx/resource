<?php

namespace App\Http\Controllers;

use App\Util\LoginUtil;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Validator;
use Session;
use App\Models\Admin;
use App\Models\Menu;
use App\Models\AdminLog;
use App\Models\Permission;

class Controller extends BaseController
{
    public $params;
    public $result;
    const SUCCESS = 200;
    const OTHER_ERROR_CODE = 0;
    public $msg = '';
    public $errorCode = 0;
    public $cache;
    public $rules = array();
    public $messages = array();
    public $customAttributes = array();
    const PAGE_SIZE = 30;
    public $pageSize;
    public $filed;
    public $order;
    public $data;

    public static $errMsg = array(
        self::SUCCESS => '操作成功',
        self::OTHER_ERROR_CODE => '操作失败',
    );

    public static $ignoreUrlArr = array(
        'auth/login' => 1,
        'admin/login' => 1,
        'admin/panel' => 1,
        'auth/logout' => 1,
        'admin/needcode' => 1,
        'upload' =>1,
    );

    //
    public function __construct(Request $request)
    {

        $this->middleware(function ($request, $next)
        {
            $id = Session::get('session_id');
            $this->params = $request->all();
            $this->field = $request->get('field') ? $request->get('field') : 'id';
            $this->order = $request->get('order') ? $request->get('order') : 'desc';
            $this->pageSize = $request->get('limit') ? $request->get('limit') : self::PAGE_SIZE;
            $this->errorCode = self::SUCCESS;
            //权限逻辑
//            $pArr = LoginUtil::PermissionGetFromRedis($id);
//            !isset($pArr['role_id']) && $pArr['role_id']  = 0;
//            $permissionArr = $pArr['permission'];
//            $_url = $request->path();
//            //是否在限制中
//            $limitOut = true;
//            $limit  = explode('/',$_url);
//            $limitAction = $limit[0] ?? 0;
//            $limitController = $limit[1] ?? 1;
//            if(!Permission::where(['action'=>$limitAction,'controller'=>$limitController])->first()){
//                //不走权限控制
//                $limitOut = false;
//            }
//            $adminPower = $this->params['adminPower'] ?? '' ;
//            if ($pArr['role_id'] == 0 &&(!isset(self::$ignoreUrlArr[$_url])) && (!isset($permissionArr[$_url])) && $adminPower!='close' &&  $limitOut) {
//                //权限判断
//                $index = strrpos($_url, '/');
//                if ($index === false) abort(403);
//                $tempUrl = explode("/", $_url);
//                $tempUrl = $tempUrl[0] . '/' . $tempUrl[1] . '/*';
//                if ($tempUrl != 'index/index/*' && !isset($permissionArr[$tempUrl]))
//                {
//                    $strs = array('status','del','delete');
//                    foreach ($strs as $str )
//                    {
//                        if (strpos($tempUrl,$str)!==false){
//                            $this->showRoleMessage(403,'权限不足！！');
//                            exit(0);
//                        }
//                    }
//                    abort(403);
//
//                }
//
//            }
//            if (!isset(self::$ignoreUrlArr[$_url])) {
//                if (strpos($_url,'list') == false && strpos($_url,'index') == false) {
//                    self::luyou($id, $_url);
//                }
//            }


            return $next($request);
        });
    }

    public function showResult($data = null)
    {
        !isset($this->errorCode) &&  $this->errorCode = 200;
        $this->result = array('error_code' =>$this->errorCode , 'data' => $data, 'msg' => $this->msg ? $this->msg : self::$errMsg[$this->errorCode]);
        header('Content-type: application/json;charset=utf-8');
        echo json_encode($this->result);
        exit(0);
    }

    public function validateParams()
    {
        $validator = Validator::make($this->params, $this->rules, $this->messages, $this->customAttributes);
        $messages = $validator->messages();
        if ($validator->fails())
        {
            foreach ($messages->toArray() as $message)
            {
                foreach ($message as $v)
                {
                    $this->msg .= $v . ';';
                }
            }
            $this->errorCode = self::OTHER_ERROR_CODE;
            $this->showResult();
        }
    }

//    public function returnJsonData($message = array())
//    {
//        header('Content-type: application/json;charset=utf-8');
//        echo json_encode($this->data ? $this->data : $message);
//        exit(0);
//    }
//
//    public function validateId($id, $checkId)
//    {
//        if (!md5(env('APP_KEY') . $id) == $checkId)
//        {
//            return abort(404);
//        }
//    }
//
//    public function showMessage($isTrue, $msg = '')
//    {
//        header('Content-type: application/json;charset=utf-8');
//        $message = array('code' => 0, 'msg' => $msg ? $msg : '操作失败');
//        if ($isTrue) $message = array('code' => 200, 'msg' => '操作成功');
//        echo json_encode($message);
//        exit(0);
//    }
//
//    public function showJsonSuccessMsg($returnData = '')
//    {
//        $message = array('code' => 200, 'msg' => '操作成功');
//        if ($returnData) $message['data'] = $returnData;
//
//        $this->returnJsonData($message);
//    }
//
//    public function showJsonFailMsg($returnData = '')
//    {
//        $message = array('code' => 0, 'msg' => '操作失败');
//        if ($returnData) $message['data'] = $returnData;
//
//        $this->returnJsonData($message);
//    }
//
//    public function moveUploadFile($sourceFile, $dstFile)
//    {
//        if (!is_dir($dstFile))
//        {
//            $index = strrpos($dstFile, '/');
//            if ($index !== false)
//            {
//                $tempFile = substr($dstFile, 0, $index);
//                if (!file_exists($tempFile)) mkdir($tempFile);
//            }
//            if (move_uploaded_file($sourceFile, $dstFile)) return true;
//        }
//
//        return false;
//    }
//
//
//    public function passUrl($arr, $secert)
//    {
//        if ($arr)
//        {
//            ksort($arr);
//            $str = '';
//            foreach ($arr as $key => $v)
//            {
//                $str .= $key . $v;
//            }
//
//            return md5($str . $secert);
//        }
//    }
//
//    //
//
//    public function showErr($item)
//    {
//        if (!$item) abort(404);
//    }
//
//    public function showJsonErrMsg($item)
//    {
//        if (!$item)
//        {
//            $message = array('code' => 0, 'msg' => '操作非法');
//            echo json_encode($message);
//            exit(0);
//        }
//    }
//
//    public function showFailJsonMes($mes = '')
//    {
//        $message = array('code' => 0, 'msg' => '操作失败');
//        if ($mes) $message['msg'] = $mes;
//
//        echo json_encode($message);
//        exit(0);
//    }
//
//
//    public function showSuccessMes($mes = '')
//    {
//        $message = array('code' => 200, 'msg' => '成功');
//        if ($mes) $message['msg'] = $mes;
//
//        echo json_encode($message);
//        exit(0);
//    }
//
//    public function showCodeMessage($code, $msg, $data = '')
//    {
//        header('Content-type: application/json;charset=utf-8');
//        $message = array('code' => $code, 'msg' => $msg, 'data' => $data);
//        echo json_encode($message);
//        exit(0);
//    }
//
//
//    public function showRoleMessage($code, $msg, $data = '')
//    {
//        header('Content-type: application/json;charset=utf-8');
//        $message = array('code' => $code, 'msg' => $msg, 'data' => $data);
//        echo json_encode($message);
//        //exit(0);
//
//    }
//
//
//    //
//    public static function luyou($admin_id, $url)
//    {
//        $id = request()->route('id');
//        $paths = explode("/", $url);
//        $params = '*';
//        if (!empty($paths[2]) && (is_numeric($paths[2]) == false)) $params = $paths[2];
//        $permission = Permission::where('action', $paths[1])->where('controller', $paths[0])->where('params', 'like', '%' . $params . '%')->first();
//        $log = new AdminLog();
//        $log->admin_id = $admin_id;
//        $log->create_at = date('Y-m-d H:i:s');
//        $log->permission_id = !empty($permission) ? $permission->id : 0;
//        if (isset($id)) $log->route_id = $id;
//        if (empty($permission)) $log->other = $url;
//        $log->save();
//    }
//
//
//    /**
//     * 异步处理队列
//     */
//    public  static  function ListStorgeToRedis($ku,$key,$value)
//    {
//        $redis = app('redis')->connection($ku);
//        $redis->rpush($key,$value);
//        return ;
//    }


}