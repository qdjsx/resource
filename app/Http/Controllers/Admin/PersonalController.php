<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PersonalController extends Controller
{
    public function index()
    {
        $id = Session::get('session_id');
        $admin = Admin::find($id);
        $status = Admin::$statusArr[$admin->status];
        return view('admin.admin.personal')->with('admin', $admin)->with('status', $status);
    }

    public function update($id, $checkId)
    {
        $id = intval($id);
        $this->validateId($id, $checkId);
        $admin = Admin::find($id);
        $this->messages = [
            "required" => ":attribute不能为空",
            "email" => ":attribute格式不正确",
        ];
        $this->customAttributes = [
            'email' => '邮箱',
            'password' => '密码',
            'sub_password' => '确认密码',
        ];
        $this->rules = [
            'email' => 'required|email',
            'password' => 'required',
            'sub_password' => 'required',
        ];
        $this->validateParams();
        $phone = $this->params['phone'];
        $email = $this->params['email'];
        $password = $this->params['password'];
        $subPassword = $this->params['sub_password'];
        $message = array('code' => 0, 'msg' => '操作失败');
        if (empty($email))
        {
            echo json_encode($message);
            exit(0);
        }
        if ($password != $subPassword)
        {
            $message['msg'] = '两次密码不一致';
        } else
        {
            if (!empty($password))
            {
                $admin->password = Hash::make($password);
            }
            $admin->email = $email;
            $admin->phone = $phone;
            $admin->save();
            $message['code'] = 200;
            $message['msg'] = '操作成功';
        }
        echo json_encode($message);
        exit(0);
    }
}
