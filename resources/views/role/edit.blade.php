@extends('list')

@section('body_content')
    <form class="layui-form" id="form_edit">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">角色名称:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="name" id="title" lay-verify="required" placeholder="请输入角色名称" autocomplete="off"
                       class="layui-input" type="text" value="{{$item->name}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">角色描述:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="display_name" id="title" lay-verify="required" placeholder="角色描述" autocomplete="off"
                       class="layui-input" type="text" value="{{$item->display_name}}">
            </div>
        </div>
        <div class="layui-form-item" pane="">
            <label class="layui-form-label" style="width: 150px;">权限</label>
            <div class="layui-input-block">
                <a class="layui-btn layui-btn-middle layui-btn-danger pre_btn2"  title="预览" category-name=''>添加权限</a>
            </div>
        </div>
        <div class="layui-form-item" pane="">
            <label class="layui-form-label" style="width: 150px;">菜单</label>
            <div class="layui-input-block">
                <a class="layui-btn layui-btn-middle layui-btn-danger pre_btn3"  title="预览" category-name=''>添加菜单</a>
            </div>
        </div>

        <input type="hidden" name="permission_ids" id="permission_ids" value="{{$permissionIds}}">
        {{--<input type="hidden" name="menu_ids" id="menu_ids" value="{{$menuIds}}">--}}
        <input type="hidden" name="menu_ids" id="menu_ids">
        <div c6lass="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>

        layui.use(['form','laydate'], function(){
            var form = layui.form,jq = layui.jquery;
            //
            form.on('submit(submit)', function(data){
                $.post("{{url('role/update')}}/{{$item->id}}",$("#form_edit").serialize(),function(data) {
                    layer.load();
                    if(data.code == 200) {
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        setTimeout(function () {
                            parent.layer.close(index); // 关闭layer
                        },0);
                        // layer.closeAll('loading');
                        // layer.msg(data.msg, {
                        //     icon:1
                        //     ,time: 500 //不自动关闭
                        //     //,btn: ['关闭', '继续操作']
                        //     ,yes: function(index){
                        //         var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        //         setTimeout(function () {
                        //             parent.layer.close(index); // 关闭layer
                        //         },1000);
                        //     }
                        // });
                    }
                    else {
                        layer.closeAll('loading');
                        layer.msg(data.msg, {time: 3000, icon:2});
                    }
                },"json");

                return false;
            });
            jq('.pre_btn2').click(function(){
                layer.open({
                    type: 2,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['80%', '80%'], //宽高
                    content: ['{{url('role/permission')}}/{{$item->id}}']//iframe的url
                });
            });
            jq('.pre_btn3').click(function(){
                console.log( $("#menu_ids").val());
                layer.open({
                    type: 2,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['90%', '90%'], //宽高
                    //content: ['{{url('role/menu')}}/{{$item->id}}']//iframe的url
                    content: ['{{url('role/menus')}}' + '?id=' + '{{$item->id}}']//iframe的url
                });
            });
        });
    </script>

@endsection