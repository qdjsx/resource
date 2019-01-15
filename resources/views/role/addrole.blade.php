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
                <textarea name="display_name" placeholder="请输入内容" class="layui-textarea" style="width: 200px;resize: none;"></textarea>
            </div>

        </div>


        <div class="layui-form-item" pane="">
            <div class="layui-input-block" id="line">
                <table>
                    <tr>
                        <td style="width: 250px;">
                            菜单：
                        </td>
                        <td>
                            模块
                        </td>
                    </tr>
                    @foreach($menus as $menu)
                        <tr>

                            <td style="width: 250px;" valign="top">
                                @if($menu->level == 1)
                                    <input type="checkbox" name="menuLevalone[]" id="one" lay-skin="primary" lay-filter="menu"
                                           value={{$menu->id}}  title={{$menu->name}} parent={!! $a = $menu->id !!}>
                                    @elseif($menu->level == 2)
                                    &emsp; &emsp;
                                    <input type="checkbox" name="menu[]" id="two" lay-skin="primary" lay-filter="menu"
                                           value={{$menu->id}}  title={{$menu->name}} level_one={{$menu->parent_id}} parent={{$menu->id}}>
                                    @elseif($menu->level ==3)
                                    &emsp; &emsp; &emsp; &emsp;
                                    <input type="checkbox" name="menu[]" id="three" lay-skin="primary" lay-filter="menu"
                                           value={{$menu->id}}  title={{$menu->name}} level_one={{$a}} level_two={{$menu->parent_id}}>
                                @endif

                            </td>
                            <td>
                                @foreach($permissions as $permission)
                                    @if($permission->menu_id == $menu->id)
                                        <input lay-filter="permission" type="checkbox" name="permissions[]"
                                               lay-skin="primary"
                                               value="{{$menu->id}}-{{$permission->function}}"
                                               title={{$per[$permission->function]}} level_one={{$a}}
                                                menuid={{$menu->id}} level_two={{$menu->parent_id}}
                                        >
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
            var $ = jquery = layui.$;

            form.on('checkbox(menu)',function (data) {
                function all(data) {
                    if (data.elem.checked == true) {
                        $("input[type='checkbox'][level_one='" + data.value + "']").prop('checked', true);
                        $("input[type='checkbox'][level_two='" + data.value + "']").prop('checked', true);
                        $("input[type='checkbox'][menuid='" + data.value + "']").prop('checked', true);
                    } else {
                        $("input[type='checkbox'][level_one='" + data.value + "']").prop('checked', false);
                        $("input[type='checkbox'][level_two='" + data.value + "']").prop('checked', false);
                        $("input[type='checkbox'][menuid='" + data.value + "']").prop('checked', false);
                    }
                }

                function second(data) {
                    if (data.elem.checked == true) {
                        $("input[type='checkbox'][parent='" + data.elem.attributes.level_one.value + "']").prop('checked', true);
                    } else {

                        var falsed = 0;
                        var sum = 0;
                        $("input[type='checkbox'][level_one='" + data.elem.attributes.level_one.value + "']").each(function () {
                            if ($(this).prop('checked') == false) falsed++;
                            sum++;
                        });
                        if (sum == falsed) $("input[type='checkbox'][parent='" + data.elem.attributes.level_one.value + "']").prop('checked', false);

                    }
                }

                function three(data) {
                    if (data.elem.checked == true) {
                        $("input[type='checkbox'][parent='" + data.elem.attributes.level_two.value + "']").prop('checked', true);
                    } else {
                        var falsed = 0;
                        var sum = 0;
                        $("input[type='checkbox'][level_two='" + data.elem.attributes.level_two.value + "']").each(function () {
                            if ($(this).prop('checked') == false) falsed++;
                            sum++;
                        });
                        if (sum == falsed) {
                            var falseds = 0;
                            var sums = 0 ;
                            $("input[type='checkbox'][parent='" + data.elem.attributes.level_two.value + "']").prop('checked', false);
                            $("input[type='checkbox'][level_one='" + data.elem.attributes.level_one.value + "']").each(function () {
                                if ($(this).prop('checked') == false) falseds++;
                                sums++
                            });
                            if (falseds == sums) $("input[type='checkbox'][parent='" + data.elem.attributes.level_one.value + "']").prop('checked', false);
                        }

                    }
                }
                if (data.elem.id == "one" || data.elem.id == "two" || data.elem.id == "three")  all(data);
                if (data.elem.id == "two" || data.elem.id == "three") second(data);
                if (data.elem.id == "three") three(data);
                form.render('checkbox');
            });

            form.on('checkbox(permission)',function (data) {
                if (data.elem.checked == true) {
                    $("input[type='checkbox'][value='" + data.elem.attributes.menuid.value + "']").prop('checked', true);
                    $("input[type='checkbox'][value='" + data.elem.attributes.level_one.value + "']").prop('checked', true);
                }else {
                    var falsed = 0;
                    var sum = 0;
                    var falseds = 0;
                    var sums = 0;
                    $("input[type='checkbox'][menuid='" + data.elem.attributes.menuid.value+ "']").each(function () {
                        if ($(this).prop('checked')==false) falsed++ ;
                        sum++;
                    });
                    $("input[type='checkbox'][level_one='" + data.elem.attributes.level_one.value+ "']").each(function () {
                        if ($(this).prop('checked')==false) falseds++;
                        sums++
                    });
                    if (sum == falsed)  $("input[type='checkbox'][value='" + data.elem.attributes.menuid.value + "']").prop('checked', false);
                    if (sums-1 == falseds)  $("input[type='checkbox'][value='" + data.elem.attributes.level_one.value + "']").prop('checked', false);
                }
                form.render('checkbox');
            });







            form.on('submit(submit)', function (data) {
                $.post("{{url('role/save')}}?_token={{csrf_token()}}", $("#form_create").serialize(), function (data) {
                    if (data.code == 200) {
                        parent.layer.closeAll();
                        parent.$(".layui-laypage-btn").click();
                    }
                    else {
                        layer.msg(data.msg, {time: 3000, icon: 2});
                        layer.closeAll('loading');
                    }
                }, "json");
                return false;
            });

        });
    </script>


@endsection