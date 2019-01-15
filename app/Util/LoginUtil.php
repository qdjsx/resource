<?php namespace App\Util;

use App\Models\Admin;
use App\Models\Menu;
use Illuminate\Filesystem\Cache;
use Session;

class LoginUtil
{
    public static $menu = array(
//        'member' => array(
//            'title' => '用户管理',
//            'sub_menu' => array(
//                array(
//                    'title' => '用户列表',
//                    'uri' => 'member/list',
//                    'icon' => '&#xe613;',
//                ),
//
//            ),
//
//        ),
//        'redPacket' => array(
//            'title' => '财务管理',
//            'sub_menu' => array(
//                array(
//                    'title' => '红包记录',
//                    'uri' => 'redPacket/list',
//                    'icon' => '&#xe65e;',
//                ),
//                array(
//                    'title' => '现金记录',
//                    'uri' => 'cash/redpkgindex',
//                    'icon' => '&#xe65e;',
//                ),
//                array(
//                    'title' => '提现记录',
//                    'uri' => 'cash/index',
//                    'icon' => '&#xe65e;',
//                ),
//                array(
//                    'title' => '微信提现记录',
//                    'uri' => 'cashExtract/index',
//                    'icon' => '&#xe65e;',
//                ),
//            ),
//        ),
        // 商品管理，分类管理
        'resource' => array(
            'title' => '资源管理',
            'sub_menu' => array(
                array(
                    'title' => '话费',
                    'uri' => 'goods/directPhone',
                    'icon' => '&#xe658;',
                ),
            ),
//            'three_class' => array(
//                array(
//                    'title' => '商品分类',
//                    'sub_menu' => array(
//                        array(
//                            'title' => '懒人分类',
//                            'uri' => 'lanrencategory/index',
//                            'icon' => '&#xe65e;',
//                        ),
//
//                    ),
//                ),
//                array(
//                    'title' => '商品推荐',
//                    'sub_menu' => array(
//                        array(
//                            'title' => '商品排序',
//                            'uri' => 'goodsSort/index',
//                            'icon' => '&#xe648;',
//                        ),
//                    ),
//                ),
//            ),

        ),
        'order' => array(
            'title' => '订单管理',
            'sub_menu' => array(
                array(
                    'title' => '手机充值订单',
                    'uri' => 'order/directPhone',
                    'icon' => '&#xe628;',
                ),
            ),
        ),
//        'system' => array(
//            'title' => '设置',
//            'sub_menu' => array(
//                array(
//                    'title' => '员工管理',
//                    'uri' => 'admin/user',
//                    'icon' => '&#xe60c;',
//                ),
//                array(
//                    'title' => '角色管理',
//                    'uri' => 'role/index',
//                    'icon' => '&#xe612;',
//                ),
//                array(
//                    'title' => '权限列表',
//                    'uri' => 'permission/index',
//                    'icon' => '&#xe658;',
//                ),
//                array(
//                    'title' => '菜单列表',
//                    'uri' => 'menu/index',
//                    'icon' => '&#xe628;',
//                ),
//                array(
//                    'title' => '管理员日志',
//                    'uri' => 'admin/log',
//                    'icon' => '&#xe637;',
//                ),
//                array(
//                    'title' => '游戏设置',
//                    'uri' => 'gameConfig/index',
//                    'icon' => '&#xe62c;',
//                ),
//            ),
//        ),
    );
    /**
     * 登录的时候，将你的菜单放到session里面
     */
    public  static  function  MenuToSession($adminId){
        $user = Admin::find($adminId);
        if($user->role_id == 1) {
            $menuAll = self::$menu;
            return $menuAll;
        }

        $menuArr = array();  //生成用户展示的菜单
        $menuKV = array();   //用户在展示菜单的key值。
        $isHandleMenuId = array();
        //用户找到角色，找到菜单。
        $roles = $user->roles;  //所有的角色
        foreach ($roles as $role){
            foreach ($role->menus as $menu){//改角色下所有菜单
                //当是三级的时候，走的是数据库，是二级的时候，走的是 public static $menu
                if (in_array($menu->id, $isHandleMenuId)) continue;   //如果存在的话，继续。
                $isHandleMenuId[] = $menu->id;
                //二级的情况下,并且有三级直接过滤
                if($menu->level == 2 && Menu::where('parent_id',$menu->id)->first())  continue;//是二级
                if($menu->level == 1) continue;

                //三级
                if ($menu->level == 3){
                    $subMenu = array(
                        'title' => $menu->name,
                        'uri' => $menu->url,
                        'icon' => '&#xe628;',
                    );
                    //先找到一级
                    $secondClass = Menu::find($menu->parent_id); //二级
                    $firstClass = Menu::find($secondClass->parent_id);  //一级
                    if (!isset($menuKV[$firstClass->id])) {    //$menuKV存储的是以及分类的大数组
                        $menuKV[$firstClass->id] = $firstClass->url;
                        $menuArr[$menuKV[$firstClass->id]]['title'] = $firstClass->name;
                    }
                    !isset($isSetM[$secondClass->id]) && $isSetM[$secondClass->id]= [];
                    array_push($isSetM[$secondClass->id],$subMenu);
                    $menuArr[$menuKV[$firstClass->id]]['three_class'][$secondClass->id] = array(
                        'title' => $secondClass->name,
                        'sub_menu' => $isSetM[$secondClass->id],
                    );
                    continue;
                }
                //纯正的二级
                if(!isset($menuKV[$menu->parent_id])){
                    $Menufirst = Menu::find($menu->parent_id);
                    if (!$Menufirst) continue;
                    $menuKV[$Menufirst->id] = $Menufirst->url;
                    $menuArr[$Menufirst->url] = array('title' => $Menufirst->name);
                }

                $menuArr[$menuKV[$menu->parent_id]]['sub_menu'][] = array(
                    'title' => $menu->name,
                    'uri' => $menu->url,
                    'icon' => '&#xe628;',
                );
            }
        }
        //重新排序
        $sortKeys = array_keys(self::$menu);
        $tempMenu = array();
        foreach ($sortKeys as $sortK) {
            if (isset($menuArr[$sortK])) $tempMenu[$sortK] = $menuArr[$sortK];
        }
        return $tempMenu;
    }

    /**
     * 权限管理
     */
    public  static  function  PermissionToRedis($adminId){
        $user = Admin::find($adminId);
        if($user->role_id == 1) {
            $re =  ['role_id'=>1,'permission'=>''];
        }else{
            $roles = $user->roles;  //所有的角色
            foreach ($roles as $role){
                foreach ($role->permissions as $permission) {
                    if ($permission->is_check == 1) {
                        $params = explode('|', $permission->params);
                        if ($params) {
                            foreach ($params as $param) {
                                $key = $permission->controller . '/' . $permission->action . ($param ? ('/' . $param) : null);
                                $permissionArr[$key] = 1;
                            }
                        }
                    }else{
                        $key = $permission->controller . '/' . $permission->action;
                        $permissionArr[$key] = 1;
                    }
                }
            }
            if (empty($permissionArr)) return abort(403);
            $re =  ['role_id'=>0,'permission'=>$permissionArr];
        }
        $redis = app('redis')->connection('default');
        $redis->setex('admin_qiandao_user_permission_'.$adminId,600,json_encode($re));
        return $re;
    }

    /**
     * 取
     * @param $adminId
     * @return mixed
     */
    public  static  function PermissionGetFromRedis($adminId){
        $redis = app('redis')->connection('default');
        $val =  $redis->get('admin_qiandao_user_permission_'.$adminId);
        if(!$val) return self::PermissionToRedis($adminId);
        return json_decode($val,true);
    }


}