<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminLogController extends Controller
{
    public function log()
    {
        $actions = AdminLog::$actionArr;
        $roles = Role::all();
        $types = Permission::groupBy('name')->orderBy('name', 'asc')->select('name')->get();


        return view('adminlog.log')->with('roles',$roles)->with('actions',$actions)->with('types',$types);
    }


    /**
     *
     */
    public function ajaxList()
    {
        $where = array();
        if (!empty($this->params['admin_id'])) $where['admin_id'] = $this->params['admin_id'];
        if (!empty($this->params['roles'])) $role_id = $this->params['roles'];
        if (!empty($this->params['action']) && $this->params['action'] == -1) $where['permission_id'] = $this->params['action'];
        $items = AdminLog::with('admin')->with('permissions')->where($where);
        if (!empty($this->params['action']) && $this->params['action'] != -1)
        {
            $permissions = Permission::where('function',$this->params['action'])->get();

            foreach ($permissions as $v)
            {
                $permissionsId[] = $v->id;
            }
            $items = $items->whereIn('permission_id',$permissionsId);

        }


        if (!empty($this->params['type']))
        {
            $permissions = Permission::where('name',$this->params['type'])->get();

            foreach ($permissions as $v)
            {
                $permissionsIds[] = $v->id;
            }
            $items = $items->whereIn('permission_id',$permissionsIds);

        }

        if (!empty($this->params['operation']))
        {
            $permissions = Permission::where('display_name', 'like', '%' . $this->params['operation'] . '%')->get();

            foreach ($permissions as $v)
            {
                $permissionsIdss[] = $v->id;
            }
            $items = $items->whereIn('permission_id',$permissionsIdss);

        }
        if (!empty($this->params['create_at']))
        {
            $regTime = explode('~', $this->params['create_at']);
            $items = $items->whereBetween('create_at', [trim($regTime[0]), trim($regTime[1])]);
        }
        $roleName = '';
        $items = $items->orderBy($this->field, $this->order)->paginate($this->pageSize);

        $data = array('code' => 0, 'msg' => '', 'count' => $items->total());
        $data['data'] = array();

        foreach ($items as $item)
        {

            $adminRoles = $item->Admin->roles;
            if (isset($role_id))
            {
                $adminRoles = $adminRoles->where('id',$role_id);
            }
            foreach ($adminRoles as $role)
            {
                $roleName = $role->name;
            }
                if (!$item->permissions) {
                    $actions = AdminLog::$actionArr[$item->permission_id]??'未知';
                }else
                {
                    $actions = AdminLog::$actionArr[$item->permissions->function]??'未知';
                }
                $data['data'][] = array(
                    'create_at' => $item->create_at,
                    'username' => $item->admin ? $item->admin->username : '',
                    'role' => $roleName,
                    'action' => $actions,
                    'type' => $item->permission_id==-1?'用户登陆':($item->permission_id == 0?'未知':$item->permissions->name),
                    'operation' => $item->permission_id==-1||$item->permission_id==0?'未知':$item->permissions->display_name.(isset($item->route_id) ? '-id:' . $item->route_id : ''),
                    'admin_id' => $item->admin_id
                );
                $roleName='';



        }

        $this->data = $data;
        $this->returnJsonData();
    }
}
