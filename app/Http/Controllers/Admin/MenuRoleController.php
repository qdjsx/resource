<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminLog;
use App\Models\Menu;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\RolePermission;
use Illuminate\Http\Request;


class MenuRoleController extends Controller
{
    public function create()
    {

        $p = self::list();



        return view('role.addrole')->with('menus', $p[0])->with('permissions', $p[1])->with('per', $p[2]);
    }

    public static function list()
    {


        $menus = new Menu();
        $functions = Permission::groupBy('menu_id', 'function')->orderBy('function', 'asc')->select('menu_id', 'function')->get();
        $menus = $menus->all();
        $menu_s = array();
        foreach ($menus as $menu)
        {
            if ($menu->level == 1)
            {
                $menu_s[] = $menu;
                foreach ($menus as $smenu)
                {
                    if ($smenu->parent_id == $menu->id)
                    {
                        $menu_s[] = $smenu;
                        foreach ($menus as $ssmenu)
                        {
                            if ($ssmenu->parent_id == $smenu->id)
                            {
                                $menu_s[] = $ssmenu;
                            }
                        }
                    }
                }
            }
        }
        $p = AdminLog::$actionArr;
        return [$menu_s, $functions, $p];
    }

    public function save(Request $request)
    {
        $params = $request->all();
        $item = new Role;
        $name = $params['name'];
        $item->name = $name;
        $item->display_name = $params['display_name'];
        $message = array('code' => 0, 'msg' => '操作失败');
        if (Role::where('name',$name)->first())
        {
            $message['msg'] = '该角色名[' . $name . ']已存在！';
            echo json_encode($message);
            exit(0);
        }
        if (!isset($params['display_name'])) $this->showMessage(0,'角色描述不能为空！');

        /**
         * 数据处理
         * menu_id - function
         * 菜单id     功能(权限)
         */
        foreach ($this->params['permissions'] as $v)
        {

            $value[] = explode("-", $v);
        }
        if ($item->save())
        {
            /**
             * 添加菜单
             * role_menu
             */
            foreach ($params['menu'] as $v)
            {
                $roleMenu = new RoleMenu;
                $roleMenu->role_id = $item->id;
                $roleMenu->menu_id = $v;
                $roleMenu->save();
            }
            //$v[0] menu_id  $v[1] function
            foreach ($value as $v)
            {
                $perid = Permission::where('menu_id', $v[0])->where("function", $v[1])->get();

                /**
                 * 添加权限
                 * role_permission_map
                 */
                foreach ($perid as $p)
                {
                    $rolePermission = new RolePermission;
                    $rolePermission->role_id = $item->id;
                    $rolePermission->permission_id = $p->id;
                    $rolePermission->save();
                }
            }
            $this->showCodeMessage(200, '添加成功');
        }
        $this->showCodeMessage(0, '添加失败');
    }

    //角色权限修改
    public function edited($id, $checkId)
    {
        $p = self::list();
        $id = intval($id);
        $this->validateId($id, $checkId);
        $role = Role::find($id);
        $roleMenus = RoleMenu::where('role_id', $id)->get();
        $rolePermissions = RolePermission::where('role_id', $id)->get();
        $permissions = Permission::all();
        $rolePers = array();
        foreach ($rolePermissions as $rolePermission)
        {
            foreach ($permissions as $permission)
            {
                if ($permission->id == $rolePermission->permission_id)
                {
                    $rolePers[] = [$permission->menu_id, $permission->function, $permission->id];
                }
            }
        }

        return view('role.edited')->with('menus', $p[0])->with('permissions', $p[1])->with('per', $p[2])->with('role', $role)->with('rolePermissions', $rolePers)->with('roleMenus', $roleMenus)->with('id', $id);

    }


    public function update($id, Request $request)
    {
        $id = intval($id);
        $params = $request->all();
        $role = Role::find($id);
        $role->name = $params['name'];
        $role->display_name = $params['display_name'];
        $role->updated_at = date('Y-m-d H:i:s');
        $role->save();
        $a = $params['menu'] ?? '';
        $b = $params['permissions'] ?? '';

        $this->dealIds($a, $id);
        if (!empty($b))
        {
            $bb = array();
            foreach ($b as $v)
            {
                $pp = explode("-", $v);
                $perid = Permission::where('menu_id', $pp[0])->where("function", $pp[1])->get();
                foreach ($perid as $per)
                {
                    $bb[] = $per->id;
                }
            }
            $this->per($bb, $id);
        }
        $this->per($b, $id);
    }


    private function dealIds($ids, $id)

    {

        //$ids，就是传过来的channel_id.将channel_id 当键。
        $slots = RoleMenu::where('role_id', $id)->get();
        $oleIds = array();
        foreach ($slots as $v)
        {
            $oleIds[$v->menu_id] = 1;
        }
        if ($ids)
        {
            foreach ($ids as $idd)
            {
                if (!$idd) continue;
                if (isset($oleIds[$idd]))
                {
                    unset($oleIds[$idd]);
                    continue;
                }
                $roleMenu = new RoleMenu;
                $roleMenu->role_id = $id;
                $roleMenu->menu_id = $idd;
                $roleMenu->save();
            }
        }

        if ($oleIds)
        {
            foreach ($oleIds as $key => $v)
            {
                $model = RoleMenu::where('role_id', $id)->where('menu_id', $key)->first();
                $model && $model->delete();
            }
        }

    }

    private function per($ids, $id)
    {
        //$ids，就是传过来的channel_id.将channel_id 当键。

        $slots = RolePermission::where('role_id', $id)->get();
        $oleIds = array();
        foreach ($slots as $v)
        {
            $oleIds[$v->permission_id] = 1;
        }
        if ($ids)
        {
            foreach ($ids as $idd)
            {
                if (!$idd) continue;
                if (isset($oleIds[$idd]))
                {
                    unset($oleIds[$idd]);
                    continue;
                }
                $rolePermission = new RolePermission;
                $rolePermission->role_id = $id;
                $rolePermission->permission_id = $idd;
                $rolePermission->save();
            }
        }

        if ($oleIds)
        {
            foreach ($oleIds as $key => $v)
            {
                $model = RolePermission::where('role_id', $id)->where('permission_id', $key)->first();
                $model && $model->delete();
            }
        }

        $this->showCodeMessage(200, '修改成功');
    }

    public function del($id,$checkId)
    {
            $id=intval($id);
            $this->validateId($id,$checkId);
            RolePermission::where('role_id',$id)->delete();
            RoleMenu::where('role_id',$id)->delete();
            Role::find($id)->delete();
            $this->showCodeMessage(200,'删除成功');
    }


}
