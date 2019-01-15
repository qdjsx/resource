<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Routing\Controller as BaseController;

class MyBaseController extends BaseController
{
    public $params ;
    public $result ;
    const SUCCESS  = 200;
    const OTHER_ERROR_CODE  = 0;
    public $msg = '';
    public $errorCode  = 0;
    public $cache ;
    public $rules = array();
    public $messages = array();
    public $customAttributes = array();
    const PAGE_SIZE = 10;
    public $pageSize ;
    public $filed;
    public $order;
    public $data;

    public  function __construct(Request $request)
    {
        $this->params = $request->all();
        $this->field = $request->get('field') ? $request->get('field') : 'id';
        $this->order = $request->get('order') ? $request->get('order') : 'desc';
        $this->pageSize = $request->get('limit') ? $request->get('limit') : self::PAGE_SIZE;
        $this->errorCode = self::SUCCESS;

        ///$this->cache =   app('cache');
        //$this->params = $request->json()->all();
        //$this->checkParams();

    }

    public function showResult($data = null)
    {
        $this->result = array('error_code' => $this->errorCode,'data' => $data,'msg' =>$this->msg ? $this->msg : self::$errMsg[$this->errorCode]);
        header('Content-type: application/json;charset=utf-8');
        echo  json_encode($this->result);
       // exit(0);
    }

    public function validateParams()
    {
        $validator = Validator::make($this->params, $this->rules,$this->messages,$this->customAttributes);
        $messages = $validator->messages();
        if ($validator->fails()) {
            foreach ($messages->toArray() as $message) {
                foreach ($message as $v){
                    $this->msg .= $v.';';
                }
            }
            $this->errorCode = self::OTHER_ERROR_CODE;
            $this->showResult();
            exit(0);
        }
    }

    public function returnJsonData($message = array())
    {
        header('Content-type: application/json;charset=utf-8');
        echo  json_encode($this->data ? $this->data : $message);
        exit(0);
    }

    public function validateId($id,$checkId)
    {
        if (!md5(env('APP_KEY').$id) == $checkId) {
            return abort(404);
        }
    }

    public function showMessage($isTrue,$msg='')
    {
        header('Content-type: application/json;charset=utf-8');
        $message =  array('code' => 0,'msg' => $msg? $msg: '操作失败');
        if($isTrue) $message = array('code' => 200,'msg' => '操作成功');
        echo  json_encode($message);
        exit(0);
    }

    public function showJsonSuccessMsg($returnData = '')
    {
        $message = array('code' => 200,'msg' => '操作成功');
        if ($returnData) $message['data'] = $returnData;

        $this->returnJsonData($message);
    }

    public function showJsonFailMsg($returnData = '')
    {
        $message = array('code' => 0,'msg' => '操作失败');
        if ($returnData) $message['data'] = $returnData;

        $this->returnJsonData($message);
    }

    public function moveUploadFile($sourceFile,$dstFile)
    {
        if (!is_dir($dstFile)) {
            $index = strrpos($dstFile,'/');
            if ($index !== false) {
                $tempFile = substr($dstFile,0,$index);
                if (!file_exists($tempFile)) mkdir($tempFile);
            }
            if(move_uploaded_file($sourceFile,$dstFile)) return true;
        }

        return false;
    }


    public function passUrl($arr,$secert)
    {
        if ($arr) {
            ksort($arr);
            $str = '';
            foreach ($arr as $key => $v){
                $str .= $key.$v;
            }

            return md5($str.$secert);
        }
    }
}
