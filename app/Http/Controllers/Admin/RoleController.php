<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Permission;
use App\Models\RoleMenu;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Menu;
use DB;

class RoleController extends Controller {
    public $result;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
        return view('role.index');
	}
    public function ajaxList(Request $request)
    {
        $params = $request->all();

        $where = '';
        if (!empty($params['title'])) $where = $params['title'];
        if ($where) {
            $items = Role::where('name','like','%'.$where.'%')->paginate($this->pageSize);
        }else{
            $items = Role::orderBy($this->field, $this->order)->paginate($this->pageSize);
        }
        $data = array('code' => 0, 'msg' => '', 'count' => $items->total());
        $data['data'] = array();
        if ($items) {
            foreach ($items as $item) {
                $data['data'][] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => $item->adminRole->count(),
                    'display_name' => $item->display_name,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                );

            }
        }
        $this->data = $data;
        $this->returnJsonData();
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return view('role.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$params = $request->all();

		$item = new Role;
		$item->name = $params['name'];
		$item->display_name = $params['display_name'];
        if($item->save()){
            $permissionIds = $request->get('permission_ids');
            if ($permissionIds) {
                $permissionIds = explode(',',$permissionIds);
                if ($permissionIds)  {
                    foreach ($permissionIds as $permissionId) {
                        if (!$permissionId) continue;
                        $rolePermissionModel = new RolePermission;
                        $rolePermissionModel->role_id = $item->id;
                        $rolePermissionModel->permission_id = $permissionId;
                        $rolePermissionModel->save();
                    }
                }
            }
            $menuIds = $request->get('menu_ids');
            if ($menuIds) {
                $menuIds = explode(',',$menuIds);
                if ($menuIds)  {
                    foreach ($menuIds as $menuId) {
                        if (!$menuId) continue;
                        $roleMenuModel = new RoleMenu;
                        $roleMenuModel->role_id = $item->id;
                        $roleMenuModel->menu_id = $menuId;
                        $roleMenuModel->save();
                    }
                }
            }
            $this->showMessage(true);
        }

		$this->showMessage(false);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id,$checkId)
	{
        $id = intval($id);
        $this->validateId($id, $checkId);
		$item = Role::find($id);
		$rolePermissions = RolePermission::where('role_id',$id)->get();
        $rolePermissionIds = array();
		if ($rolePermissions) {
		    foreach ($rolePermissions as $rolePermission) {
		        $rolePermissionIds[] = $rolePermission->permission_id;
            }
        }
        $roleMenus = RoleMenu::where('role_id',$id)->get();
        $roleMenuIds = array();
        if ($roleMenus) {
            foreach ($roleMenus as $roleMenu) {
                $roleMenuIds[] = $roleMenu->menu_id;
            }
        }

		return view('role.edit')->with('item',$item)->with('permissionIds',implode(',',$rolePermissionIds))
            ->with('menuIds',implode(',',$roleMenuIds));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request ,$id)
	{
		$id = intval($id);

		$item = Role::find($id);

		$item->name = $request->get('name');
		$item->display_name = $request->get('display_name');
        $permissionIds = $request->get('permission_ids');
        $menuIds = $request->get('menu_ids');
        $hasSelectPermissionIds =  $hasSelectRoleIds = array();
        $permissionList = RolePermission::where('role_id',$id)->get();
        $menuList = RoleMenu::where('role_id',$id)->get();
        if ($permissionList) {
            foreach ($permissionList as $permission) {
                $hasSelectPermissionIds[$permission->permission_id] = 1;
            }
        }
        if ($menuList) {
            foreach ($menuList as $menu) {
                $hasSelectRoleIds[$menu->menu_id] = 1;
            }
        }
        if ($permissionIds) {
            $permissionIds = explode(',',$permissionIds);
            if ($permissionIds)  {
                foreach ($permissionIds as $permissionId) {
                    if (!$permissionId) continue;
                    if (isset($hasSelectPermissionIds[$permissionId])) {
                        unset($hasSelectPermissionIds[$permissionId]);
                        continue;
                    }
                    $rolePermissionModel = new RolePermission;
                    $rolePermissionModel->role_id = $id;
                    $rolePermissionModel->permission_id = $permissionId;
                    $rolePermissionModel->save();
                }
            }
        }
        if ($menuIds) {
            $menuIds = explode(',',$menuIds);
            if ($menuIds)  {
                foreach ($menuIds as $menuId) {
                    if (!$menuId) continue;
                    if (isset($hasSelectRoleIds[$menuId])){
                        unset($hasSelectRoleIds[$menuId]);
                        continue;
                    }
                    if(Menu::find($menuId)->is_parent == 1) continue;
                    $roleMenuModel = new RoleMenu;
                    $roleMenuModel->role_id = $id;
                    $roleMenuModel->menu_id = $menuId;
                    $roleMenuModel->save();
                }
            }
        }
        if ($hasSelectPermissionIds) {
            foreach($hasSelectPermissionIds as $key => $v) {
                $model = RolePermission::where('role_id',$id)->where('permission_id',$key)->first();
                 $model && $model->delete();
            }
        }
        if ($hasSelectRoleIds) {
            foreach ($hasSelectRoleIds as $key => $v) {
                $model = RoleMenu::where('role_id',$id)->where('menu_id',$key)->first();
                $model && $model->delete();
            }
        }

		$this->showMessage($item->save());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    public function permission($id)
    {
        $items = Permission::where('parent_id',0)->get();
        $geoArr = array();
        if ($items) {
            foreach ($items as $item) {
                $list = Permission::where('parent_id',$item->id)->get();
                $tmpList = $list->toArray();
                if (empty($tmpList)) {
                    $geoArr[] = array('parent_code' => $item->id,'value' => $item->name,'name' => $item->name);
                    continue;
                }
                $tmp = array();
                foreach ($list as $v) {
                    $tmp[] = array('code' => $v->id,'name' => $v->name);
                }
                $geoArr[] = array('parent_code' => $item->id,'value' => $tmp,'name' => $item->name);
            }
        }
        $selectCategoryIds = array();
        if ($id) {
            $rolePermissions = RolePermission::where('role_id',$id)->get();
            if ($rolePermissions) {
                foreach ($rolePermissions as $rolePermission) {
                    $selectCategoryIds[$rolePermission->permission_id] = 1;
                }
            }
        }

        return view('role.category')->with('geoArr',$geoArr)->with('geoCodes',$selectCategoryIds);

    }

    public function menu($id)
    {
        $items = Menu::where('parent_id',0)->get();
        $geoArr = array();
        if ($items) {
            foreach ($items as $item) {
                $list = Menu::where('parent_id',$item->id)->get();
                $tmpList = $list->toArray();
                if (empty($tmpList)) {
                    $geoArr[] = array('parent_code' => $item->id,'value' => $item->name,'name' => $item->name);
                    continue;
                }
                $tmp = array();
                foreach ($list as $v) {
                    $tmp[] = array('code' => $v->id,'name' => $v->name);
                }
                $geoArr[] = array('parent_code' => $item->id,'value' => $tmp,'name' => $item->name);
            }
        }
        $selectCategoryIds = array();
        if ($id) {
            $rolePermissions = RoleMenu::where('role_id',$id)->get();
            if ($rolePermissions) {
                foreach ($rolePermissions as $rolePermission) {
                    $selectCategoryIds[$rolePermission->menu_id] = 1;
                }
            }
        }

        return view('role.menu')->with('geoArr',$geoArr)->with('geoCodes',$selectCategoryIds);

    }

    public  function  menus(){

	    return view('role.menus');
    }
    public  function ajaxListMenus(Request $request){
        $roleId = $request->get('id');
        $menus = RoleMenu::where('role_id',$roleId)->get();
        $result = array();
        foreach ($menus as  $v){
           $result[$v->menu_id] = 1;
        }
        $this->result = $result;
        $items = Menu::where(['level'=>1])->orderBy('id')->get();
        $data = array();
        foreach ($items as $item){
            $data['data'][]= [
                'id' => $item->id,
                'text' => $item->name,
                'state'=> array(
                    'opened' =>  false,       //false，默认子关闭，true打开
                    'disabled' => false ,    //false可点击。设置为true,相当于置黑 (isset($catsAll[$item->cid]) && $catsAll[$item->cid] != $lanrenId ? true : false)
                    "selected"=>  isset($this->result[$item->id]) ? true: false,  //true 默认选中
                ),
                'children' => $item->is_parent == 1 ? $this->searchChild($item->id) : false,
            ];
        }
        $this->data = $data;

        $this->returnJsonData();
    }
    //递归遍历jstree
    public function  searchChild($id){
        $items =  Menu::where('parent_id',$id)->orderBy('id')->get();
        if(empty($items->toArray())){
            $menu = Menu::find($id);
            $menu->is_parent = 0;
            $menu->save();
        }
        $data = array();
        foreach ($items as $key => $item){
            if( $item->is_parent == 0) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'state'=> array(
                        'opened' =>  false,
                        'disabled' => false,
                        "selected"=> isset($this->result[$item->id]) ? true: false,//isset($catsAll[$item['cid']]) && $catsAll[$item['cid']] == $lanrenId ? true : false ,
                    ),
                    'children' => false,
                ];
                unset($items[$key]);
                continue;
            } else{
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'state'=> array(
                        'opened' =>  false,       //
                        'disabled' => false,
                        "selected"=> isset($this->result[$item->id]) ? true: false,//isset($catsAll[$item['cid']]) && $catsAll[$item['cid']] == $lanrenId  ? true : false ,
                    ),
                    'children' => $this->searchChild($item->id),
                ];
                unset($items[$key]);
            }
        }
        return $data;
    }

}
