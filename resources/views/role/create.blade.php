@extends('list')
@section('body_content')
    <style>
        #per .layui-form-checkbox {
            float: left;
        }
    </style>
    <form class="layui-form" id="form_create">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">角色名称:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="name" id="title" lay-verify="required" placeholder="请输入角色名称" class="layui-input"
                       type="text" value="" style="width: 200px">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">角色描述:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="display_name" id="title" lay-verify="required" placeholder="角色描述" autocomplete="off"
                       class="layui-input" type="text" value="" style="width: 200px">
            </div>

        </div>


        <div class="layui-form-item" pane="">
            <div class="layui-input-block" id="line">
                <table>
                    <tr>
                        <td style="width: 250px;">
                            权限：
                        </td>
                        <td>
                            模块
                        </td>
                    </tr>
                    @foreach($menus as $menu)
                        <tr>

                            <td style="width: 250px;" valign="top">
                                @if($menu->level == 2)
                                &emsp; &emsp;
                                @elseif($menu->level ==3)
                                &emsp; &emsp; &emsp; &emsp;
                                @endif
                                <input type="checkbox" name="menu[]" lay-skin="primary" lay-filter="menu"
                                       value={{$menu->id}}  title={{$menu->name}}>
                            </td>
                            <td>
                                @foreach($permissions as $permission)
                                    @if($permission->menu_id == $menu->id)
                                        <input lay-filter="permission" type="checkbox" name="permissions[]"
                                               menuId={{$menu->id}}  lay-skin="primary"
                                               value="{{$menu->id}}-{{$permission->function}}"
                                               title={{$per[$permission->function]}}>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>

        layui.use(['form', 'jquery', 'table'], function () {
            var form = layui.form;
            var table = layui.table;
            var $ = jquery = layui.$;
            form.on('checkbox(menu)', function (data) {
                if (data.elem.checked == true) {
                    $("input[type='checkbox'][menuId='" + data.value + "']").prop('checked', true);
                } else {
                    $("input[type='checkbox'][menuId='" + data.value + "']").prop('checked', false);

                }
                form.render('checkbox');
            });
            form.on('submit(submit)', function (data) {
                $.post("{{url('role/')}}?_token={{csrf_token()}}", $("#form_create").serialize(), function (data) {
                    layer.load();
                    if (data.code == 200) {
                        layer.closeAll();
                        parent.layer.msg(data.msg, {
                            icon: 1
                            , time: 3000
                            , btn: ['关闭', '继续操作']
                            , yes: function (index) {
                                parent.layer.closeAll();
                                parent.location.reload();

                            },


                        });
                    }
                    else {
                        layer.closeAll('loading');
                        layer.msg(data.msg, {time: 3000, icon: 2});
                    }
                }, "json");
                return false;
            });
        });
    </script>


@endsection