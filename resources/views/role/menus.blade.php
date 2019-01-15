@extends('list')
@section('body_content')
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="{{asset('/treejs/style.min.css')}}">
<script type="text/javascript" src="{{asset('/treejs/jstree.min.js')}}"></script>
<!--搜索功能-->
<div class="layui-form-query">
    <form id="search" class="layui-form" >
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">菜单名称：</label>
                <div class="layui-input-inline">
                    <input type="search" id="catsName" class="layui-input"/>
                </div>
            </div>

            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i>搜索
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<!--页面展示-->
<div class="reason_list">
    <div id="treeview" class="treeview">
    </div>
</div>
<script>
    $("#treeview").jstree({
        'core' : {
            //"multiple" : false,   //这个控制，点一下，是否取消别的点击。
            "themes" : { "icons": false }, //出去文件夹的图标
            'dblclick_toggle': true,       //禁用tree的双击展开
            'data' : function (obj, callback) {
                $.ajax({
                    type: "post",
                    url:"{{url("roleMenu/ajaxList")}}?_token={{csrf_token()}}&id={{request()->get('id')}}",
                    dataType:"json",
                    async: true,
                    success:function(result) {
                        callback(result.data);
                    }
                });
            }
        },
        "plugins" : ["checkbox","search"],
        "checkbox":{
            cascade_to_disabled : false
        }

    });
    $('#treeview').on("changed.jstree", function (e, data) {
        var ids = data.selected;
        parent.layui.jquery("#menu_ids").val(ids);//给父页面赋值

    });

    $("#search").submit(function(e) {
        e.preventDefault();
        $("#treeview").jstree(true).search($("#catsName").val());
    });

</script>
@endsection