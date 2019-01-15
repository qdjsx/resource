<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminLog;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
        return view('permission.index');
	}
    public function ajaxList(Request $request)
    {
        $params = $request->all();
        if (!empty($params['title'])) {
            $items = Permission::where('name', 'like', '%' . $params['title'] . '%')->paginate($this->pageSize);
        }else{
            $items = Permission::paginate($this->pageSize);
        }
        //
        $data = array('code' => 0, 'msg' => '', 'count' => $items->total());
        $data['data'] = array();
        if ($items) {
            foreach ($items as $item) {
                $data['data'][] = array(
                    'id' => $item->id,
                    'menu_id' => $item->menu_id .'---'.($item->PerMenu ? $item->PerMenu->name : ''),
                    'name' => $item->name,
                   // 'created_at' => $item->created_at?$item->created_at->format('Y-m-d H:i:s'):'',
                    //'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
                    'controller' => $item->controller,
                    'action' =>$item->action,
                    'function'=>AdminLog::$actionArr[$item->function],
                    'is_check' => $item->is_check ==1 ? '是' : '否',
                    'params' => $item->params,
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
		return view('permission.create')->with('parents',Menu::all())->with('function',AdminLog::$actionArr);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
    {
        $params = $request->all();
        $item = new Permission;
        $item->name = $params['name'];
        $item->display_name = $params['display_name'];
        $item->menu_id = $params['menu_id'];
        $item->action = $params['action'];
        $item->controller = $params['controller'];
        $item->params = $params['params'];
        $item->is_check = $params['is_check'];
        $item->function = $params['function'];
        $this->showMessage($item->save());
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
	public function edit($id, $checkId)
	{
        $id = intval($id);
        $this->validateId($id, $checkId);
		$item = Permission::find($id);

		return view('permission.edit')
            ->with('item',$item)->with('parents',Menu::all())->with('function',AdminLog::$actionArr);
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
		$item  = Permission::find($id);
		$params = $request->all();
        $item->name = $params['name'];
        $item->display_name = $params['display_name'];
        $item->menu_id = $params['menu_id'];
        $item->action = $params['action'];
        $item->controller = $params['controller'];
        $item->params = $params['params'];
        $item->is_check = $params['is_check'];
        $item->function = $params['function'];
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
