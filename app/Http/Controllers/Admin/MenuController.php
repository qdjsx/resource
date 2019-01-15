<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view('menu.index');
    }
    public function ajaxList(Request $request)
    {

        $params = $request->all();
        $where = array();
        if (!empty($params['title'])) $where['name'] = $params['title'];
        if ($where) {
            $items = Menu::where($where)->paginate($this->pageSize);
        }else{
            $items = Menu::paginate($this->pageSize);
        }
        $data = array('code' => 0, 'msg' => '', 'count' => $items->total());
        $data['data'] = array();
        if ($items) {
            foreach ($items as $item) {
                $data['data'][] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'url' => $item->url,
                    'level' => $item->level,
                    'parent_id' => $item->parent_id,
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
        $levelArr = array(1 => '一级菜单',2 => '二级菜单',3=>'三级菜单');

        return view('menu.create')->with('parents',Menu::where('level', '!=', 3)->get())
            ->with('levelArr',$levelArr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $params  = $request->all();
        if($params['level'] == 1){
            $isParent = 1;
        }else if($params['level'] == 2){
            $isParent = 0;
        }else if($params['level'] == 3){
            $isParent = 0;
            $parentId = $params['parent_id'];
            $menu = Menu::find($parentId);
            $menu->is_parent = 1;
        }

        $item = new Menu;
        $item->name = $params['name'];
        $item->url = $params['url'];
        $item->parent_id = $params['parent_id'];
        $item->level = $params['level'];
        $item->is_parent = $isParent;

        $this->showMessage($item->save() && isset($menu) ? $menu->save() : true);
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
        $item = Menu::find($id);
        $levelArr = array(1 => '一级菜单',2 => '二级菜单',3=>'三级菜单');

        return view('menu.edit')->with('item',$item)->with('parents',Menu::where('level', '!=', 3)->get())
            ->with('levelArr',$levelArr);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $id = intval($id);
        $params = $request->all();
        if($params['level'] == 1 && $params['parent_id'] != 0){
            $this->showMessage('','一级菜单不能有父id');
        }
        $item = Menu::find($id);
        $item->name = $params['name'];
        $item->url = $params['url'];
        if($item->level == $params['level'] && $item->parent_id == $params['parent_id']){
            $this->showMessage($item->save());
        }
        //变化的话，判断是不是父类
        if($item->is_parent == 1){
            //不支持有子分类变成3级水平
            if($params['level'] == 3) $this->showMessage('','不支持有子分类变成三级菜单');
            //不支持一级大分类变成别的子类
            if($item->level ==1)  {
                $its = Menu::where('parent_id',$id)->get();
                foreach ($its as $it){
                    if($it->is_parent == 1) $this->showMessage('','不支持将一级大分类(三层)变成其他的子类');
                    continue;
                }

            }
            //如果他本身是父亲的话，变化层级的话，需要将子类的层级在他基础上加1
            $its = Menu::where('parent_id',$id)->get();
            foreach ($its as $it){
                $it->level = $params['level'] + 1;
                $it->save();
            }

        }
        if($item->is_parent == 0 && $params['level'] == 3) {
            $parentId = $params['parent_id'];
            $menu = Menu::find($parentId);
            $menu->is_parent = 1;
            $menu->save();
        }
        $item->parent_id = $params['parent_id'];
        $item->level = $params['level'];
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

}
