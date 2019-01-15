<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

	protected  $table = 'menu';


    /**
     *执行save方法的时候，将你的记录保存下来。
     */
    public function save(array $options = [])
    {
        $id = empty($this->id) ? '' : $this->id;
        $item = Menu::find($id);
        $before = Activity::updateBefore('menu',$id,json_encode($item));
        $RelSave = parent::save();  //返回结果是true
        if($RelSave){
            $id = empty($this->id) ? '' : $this->id;
            $item = Menu::find($id);
            Activity::updateAfter(json_encode($item) ,$before);
            return true;
        }
        return false;
    }

}
