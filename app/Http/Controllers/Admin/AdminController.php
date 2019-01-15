<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Role;
use App\Models\AdminRole;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function index()
    {
        $item = Admin::find(Session::get('session_id'));
        return view('app')->with('item',$item);
    }

    public function test()
    {
        return view('index');
    }

    private static function dealData($item, $yesterdayItem)
    {
        $column = array(
            'all_total_user' => '用户总量',
            'all_today_new' => '今日新增',
            'all_today_active' => '今日活跃',
            'all_3days_active' => '连续3日活跃',
            'all_7days_active' => '连续7日活跃',
            'in_3days_active' => '近3日活跃',
            'in_7days_active' => '近7日活跃',
        );
        $data = array();
        foreach ($column as $v => $value)
        {
            if ($yesterdayItem->$v > $item->$v)
            {
                $data[$v] = array(
                    'count' => $item->$v,
                    'icon' => 'text-danger',
                    'desc' => 'fa-level-down',
                    'rat' => $yesterdayItem->$v ? round(100 * ($yesterdayItem->$v - $item->$v) / $yesterdayItem->$v, 2) : 0,
                    'info' => $value,
                );
            } else
            {
                $data[$v] = array(
                    'count' => $item->$v,
                    'icon' => 'text-navy',
                    'desc' => 'fa-level-up',
                    'rat' => $yesterdayItem->$v ? round(100 * ($item->$v - $yesterdayItem->$v) / $yesterdayItem->$v, 2) : 0,
                    'info' => $value,
                );
            }
        }

        return $data;
    }

    private static function dealDataBh($item, $yesterdayItem)
    {
        $column = array(
            'all_pv' => '页面访问总量',
            'all_uv' => '访问人数',
        );
        $data = array();
        foreach ($column as $v => $value)
        {
            if ($item && $yesterdayItem && $yesterdayItem->$v > $item->$v)
            {
                $data[$v] = array(
                    'count' => $item->$v,
                    'icon' => 'text-danger',
                    'desc' => 'fa-level-down',
                    'rat' => $yesterdayItem->$v ? round(100 * ($yesterdayItem->$v - $item->$v) / $yesterdayItem->$v, 2) : 0,
                    'info' => $value,
                );
            } else
            {
                $data[$v] = array(
                    'count' => $item->$v,
                    'icon' => 'text-navy',
                    'desc' => 'fa-level-up',
                    'rat' => $yesterdayItem->$v ? round(100 * ($item->$v - $yesterdayItem->$v) / $yesterdayItem->$v, 2) : 0,
                    'info' => $value,
                );
            }
        }

        return $data;
    }

    private static function dealDataOrder($item, $yesterdayItem)
    {
        $column = array(
            'order_cnt' => '省钱购兑换总量',
            'order_uv' => '省钱购兑换人数',
            'order_app_price' => '省钱购订单总额',
            //'all_cancelled_order' => '省钱购实际订单数',
            //'all_order_per_user' => '省钱购佣金总额',
            'zero_buy_cnt' => '零元购兑换量',
            'zero_buy_uv' => '零元购兑换人数',
        );
        $data = array();
        foreach ($column as $v => $value)
        {
            if ($item && $yesterdayItem && $yesterdayItem->$v > $item->$v)
            {
                $data[$v] = array(
                    'count' => $item->$v,
                    'icon' => 'text-danger',
                    'desc' => 'fa-level-down',
                    'rat' => $item && $yesterdayItem && $yesterdayItem->$v ? round(100 * ($yesterdayItem->$v - $item->$v) / $yesterdayItem->$v, 2) : 0,
                    'info' => $value,
                );
            } else
            {
                $data[$v] = array(
                    'count' => $item && $item->$v ? $item->$v : 0,
                    'icon' => 'text-navy',
                    'desc' => 'fa-level-up',
                    'rat' => $item && $yesterdayItem && $yesterdayItem->$v ? round(100 * ($item->$v - $yesterdayItem->$v) / $yesterdayItem->$v, 2) : 0,
                    'info' => $value,
                );
            }
        }

        return $data;
    }

    //管理员管理
    public function user()
    {

        $roles = Role::all();
        return view('admin.admin.indexs')->with('roles', $roles);

    }

    //
    public function ajaxList(Request $request)
    {
        $params = $request->all();

        $where = array();


        if (!empty($params['phoneNumber'])) $where['phone'] = $params['phoneNumber'];
        if (!empty($params['status'])) $where['status'] = $params['status'];
        if (!empty($params['role_id'])) $role_id = $params['role_id'];
        if (!empty($params['role'])) $role_id = $params['role'];
        $items = new Admin;
        $items = $items->where($where);
        if (!empty($params['username']))
        {
            $items = $items->where('username','like','%'.$params['username'].'%');
        }
        if (!empty($params['email']))
        {
            $items = $items->where('email', 'like', '%' . $params['email'] . '%');
        }

        if (!empty($params['updated_at']))
        {
            $regTime = explode('~', $params['updated_at']);
            $items = $items->whereBetween('updated_at', [trim($regTime[0]), trim($regTime[1])]);
        }


        $items = $items->orderBy($this->field, $this->order)->paginate($this->pageSize);
        $data = array('code' => 0, 'msg' => '', 'count' => $items->total());
        $data['data'] = array();
        $name = array();


        foreach ($items as $item)
        {





            $adminRoles = $item->roles;
            foreach ($adminRoles as $role)
            {
                $name[] = $role->name;
            }

            if (isset($role_id))
            {
                $adminRoles = $adminRoles->where('id', $role_id);

                foreach ($adminRoles as $role)
                {
                    $data['data'][] = array(
                        'id' => $item->id,
                        'username' => $item->username,
                        'phoneNumber' => $item->phone,
                        'role' => $role->name,
                        'email' => $item->email,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                        'status' => $item->getStatus(),
                        'department' => $item->department
                    );
                }
            }else{
                $data['data'][] = array(
                    'id' => $item->id,
                    'username' => $item->username,
                    'phoneNumber' => $item->phone,
                    'role' => $name,
                    'email' => $item->email,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                    'status' => $item->getStatus(),
                    'department' => $item->department
                );
                $name = null;
            }


        }


        $this->data = $data;
        $this->returnJsonData();
    }

    public function createuser()
    {
        $roles = Role::all();

        return view('admin.admin.created')->with("roles", $roles);
    }

    //
    public function save()
    {
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
        $email = $this->params['email'];
        $password = $this->params['password'];
        $subPassword = $this->params['sub_password'];
        $message = array('code' => 0, 'msg' => '操作失败');
        if (empty($email) || empty($password) || empty($subPassword))
        {
            echo json_encode($message);
            exit(0);
        }
        if ($password != $subPassword)
        {
            $message['msg'] = '两次密码不一致';
        } else
        {
            if (Admin::where('email', $email)->first())
            {
                $message['msg'] = '该用户[' . $email . ']已经被注册';
            } else
            {
                $user = new Admin;
                $user->role_id = 0;
                $user->email = $email;
                $user->password = Hash::make($password);
                $user->created_at = date('Y-m-d H:i:s', time());
                $user->save();
                $message['code'] = 200;
                $message['msg'] = '操作成功';
            }
        }
        echo json_encode($message);
        exit(0);
    }

    //编辑用户
    public function edituser($id, $checkId)
    {
        $id = intval($id);
        $this->validateId($id, $checkId);
        $item = Admin::findOrFail($id);
        return view('admin.admin.edit')->with('item', $item);
    }

    //
    public function updateuser($id, $checkId)
    {
        $id = intval($id);
        $this->validateId($id, $checkId);
        $email = $this->params['email'];
        $password = $this->params['password'];
        $subPassword = $this->params['sub_password'];
        $phone = $this->params['phone'];
        $department = $this->params['department'];
        $username = $this->params['username'];
        $status = $this->params['status'];
        $message = array('code' => 0, 'msg' => '操作失败');

        if ($password != $subPassword)
        {
            $message['msg'] = '两次密码不一致';
        } else
        {
            $user = Admin::find($id);
            if (!empty($password) && !empty($subPassword))
            {
                $user->password = Hash::make($password);
            }

            $user->email = $email;
            $user->username = $username;
            $user->phone = $phone;
            $user->status = $status;
            $user->department = $department;

            $user->save();
            $message['code'] = 200;
            $message['msg'] = '操作成功';
        }
        echo json_encode($message);
        exit(0);
    }

    //
    public function permission($id, $checkId)
    {
        $id = intval($id);
        $this->validateId($id, $checkId);
        $items = Role::all();
        $geoArr = array();
        if ($items)
        {
            foreach ($items as $item)
            {
                $geoArr[] = array('parent_code' => $item->id, 'value' => $item->name, 'name' => $item->name);
            }
        }
        $selectRoleIds = array();
        if ($id)
        {
            $userRoles = AdminRole::where('admin_id', $id)->get();
            if ($userRoles)
            {
                foreach ($userRoles as $userRole)
                {
                    $selectRoleIds[$userRole->role_id] = 1;
                }
            }
        }

        return view('admin.admin.menu')->with('geoArr', $geoArr)->with('geoCodes', $selectRoleIds)
            ->with('roleIds', implode(',', array_keys($selectRoleIds)))
            ->with('item', Admin::find($id));

    }

    public function updatePermission(Request $request, $id)
    {
        $id = intval($id);
        $user = Admin::find($id);
        $this->showErr($user);
        $roleIds = $request->get('role_ids');
        $userRoles = AdminRole::where('admin_id', $id)->get();

        $hasSelectRoleIds = array();
        if ($userRoles)
        {
            foreach ($userRoles as $userRole)
            {
                $hasSelectRoleIds[$userRole->role_id] = 1;

            }
        }
        if ($roleIds)
        {
            $roleIds = explode(',', $roleIds);
            if ($roleIds)
            {
                foreach ($roleIds as $roleId)
                {
                    if (!$roleId) continue;
                    if (isset($hasSelectRoleIds[$roleId]))
                    {
                        unset($hasSelectRoleIds[$roleId]);
                        continue;
                    }
                    $item = new AdminRole;
                    $item->admin_id = $id;
                    $item->role_id = $roleId;
                    $item->save();
                }
            }
        }
        if ($hasSelectRoleIds)
        {
            foreach ($hasSelectRoleIds as $key => $roleId)
            {
                $item = AdminRole::where('admin_id', $id)->where('role_id', $key)->first();
                $item && $item->delete();
            }
        }

        $this->showSuccessMes();

    }

    public function status($id, $checkId)
    {
        $id = intval($id);
        $this->validateId($id, $checkId);
        $user = Admin::find($id);
        if ($user->status == Admin::VALID_STATUS)
        {
            $user->status = Admin::INVALID_STATUS;

            if ($user->save())
            {
                $this->showCodeMessage(200, '操作成功');
            }
            $this->showCodeMessage(0, '操作失败');
        } else if ($user->status == Admin::INVALID_STATUS)
        {
            $user->status = Admin::VALID_STATUS;
            if ($user->save())
            {
                $this->showCodeMessage(200, '操作成功');
            }
            $this->showCodeMessage(0, '操作失败');
        }
    }

    public function saves()
    {
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
        $email = $this->params['email'];
        $password = $this->params['password'];
        $subPassword = $this->params['sub_password'];
        $phone = $this->params['phone'];
        $department = $this->params['department'];
        $role = $this->params['roles'];
        $username = $this->params['username'];
        $status = $this->params['status'];
        $message = array('code' => 0, 'msg' => '操作失败');
        if (empty($email) || empty($password) || empty($subPassword))
        {
            echo json_encode($message);
            exit(0);
        }
        if ($password != $subPassword)
        {
            $message['msg'] = '两次密码不一致';
        } else
        {
            if (Admin::where('email', $email)->first())
            {
                $message['msg'] = '该用户邮箱[' . $email . ']已经被注册';
            }
            if(Admin::where('username',$username)->first())
            {
                $message['msg'] = '该用户名[' . $username . ']已经被注册';
            }
            else
            {

                $user = new Admin;
                $user->role_id = 0;
                $user->email = $email;
                $user->username = $username;
                $user->phone = $phone;
                $user->status = $status;
                $user->department = $department;
                $user->password = Hash::make($password);
                $user->created_at = date('Y-m-d H:i:s', time());
                $message['code'] = 200;
                $message['msg'] = '操作成功';
                $user->save();
                if ($user->status == 1 && !empty($role))
                {
                    $roles = new AdminRole;
                    $roles->admin_id = $user->id;
                    $roles->role_id = $role;
                    $roles->save();
                }
            }
        }
        echo json_encode($message);
        exit(0);
    }

    public function del($id, $checkId)
    {
        $id = intval($id);
        $this->validateId($id, $checkId);
        $admin = Admin::find($id);
        $adminRole = AdminRole::where('admin_id', $id);
        if (!empty($adminRole))
        {
            $adminRole->delete();
        }
        if ($admin->delete())
        {
            $this->showCodeMessage(200, '删除成功');
        }
        $this->showCodeMessage(0, '删除失败');
    }
    //跳转员工管理界面
    public function roleuser($id)
    {

        $roles = Role::all();
        return view('admin.admin.indexs')->with('roles', $roles)->with('role_id',$id);

    }

}
