<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\GoodsDirectPhone;


class GoodsDirectPhoneController extends Controller
{
    //话费首页
    public  function index(){

        $a = GoodsDirectPhone::get();
        var_dump($a);
        return view('admin.directphone.list');
    }

}
