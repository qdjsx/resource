<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model {

	protected  $table = 'role_menu';


    /**
     *执行save方法的时候，将你的记录保存下来。
     */
    public function save(array $options = [])
    {
        $id = empty($this->id) ? '' : $this->id;
        $item = RoleMenu::find($id);
        $before = Activity::updateBefore('role_menu',$id,json_encode($item));
        $RelSave = parent::save();  //返回结果是true
        if($RelSave){
            $id = empty($this->id) ? '' : $this->id;
            $item = RoleMenu::find($id);
            Activity::updateAfter(json_encode($item) ,$before);
            return true;
        }
        return false;
    }

}
