<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

	protected  $table = 'role';

	public function  menus()
    {
        return $this->belongsToMany('App\Models\Menu','role_menu','role_id','menu_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission','role_permission_map','role_id','permission_id');
    }

    public function adminRole()
    {
        return $this->hasMany('App\Models\AdminRole','role_id','id');
    }


    /**
     *执行save方法的时候，将你的记录保存下来。
     */
    public function save(array $options = [])
    {
        $id = empty($this->id) ? '' : $this->id;
        $item = Role::find($id);
        $before = Activity::updateBefore('role',$id,json_encode($item));
        $RelSave = parent::save();  //返回结果是true
        if($RelSave){
            $id = empty($this->id) ? '' : $this->id;
            $item = Role::find($id);
            Activity::updateAfter(json_encode($item) ,$before);
            return true;
        }
        return false;
    }


}
