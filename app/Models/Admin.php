<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected  $table = 'admin';

    const ROLE_ADMIN = 1;

    const VALID_STATUS = 1;
    const INVALID_STATUS = -1;


    public static $statusArr = array(
        self::VALID_STATUS => '正常',
        self::INVALID_STATUS => '<a style="color: red">禁用</a>',
    );
    public function getStatus()
    {
        return self::$statusArr[$this->status];
    }




    public function roles()
    {
        return $this->belongsToMany('App\Models\Role','admin_role_map','admin_id','role_id');
    }


    /**
     *执行save方法的时候，将你的记录保存下来。
     */
    public function save(array $options = [])
    {
        $id = empty($this->id) ? '' : $this->id;
        $item = Admin::find($id);
        $before = Activity::updateBefore('admin',$id,json_encode($item));
        $RelSave = parent::save();  //返回结果是true
        if($RelSave){
            $id = empty($this->id) ? '' : $this->id;
            $item = Admin::find($id);
            Activity::updateAfter(json_encode($item) ,$before);
            return true;
        }
        return false;
    }

    public function roless()
    {
        return $this->hasMany('App\Models\AdminRole');
    }



}
