@extends('list')
@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline ">
                            <label class="layui-form-mid">用户名：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="username" id="username" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline ">
                            <label class="layui-form-mid">角色：</label>
                            <div class="layui-input-inline">
                                <select name="role" id="role" lay-filter="role" lay-search="">
                                    <option value="">全部</option>
                                    @foreach($roles as $role)
                                        <option value={{$role->id}}>{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(isset($role_id))
                            <input type="hidden" name = "role" value="{{$role_id}}" id="role">
                            {{$role_id}}
                        @endif
                        <div class="layui-inline ">
                            <label class="layui-form-mid">邮箱：</label>
                            <div class="layui-input-inline">
                                <input type="email" name="email" id="email" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline ">
                            <label class="layui-form-mid">手机号：</label>
                            <div class="layui-input-inline">
                                <input type="tel" name="phoneNumber" id="phoneNumber" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <label class="layui-form-mid">修改时间：</label>
                            <div class="layui-input-inline" >
                                <input type="text" name="updated_at" id="updated_at" autocomplete="off"
                                       class="layui-input fsDate"
                                       daterange="1" placeholder="~">
                            </div>
                        </div>

                        <div class="layui-inline ">
                            <label class="layui-form-mid">状态：</label>
                            <div class="layui-input-inline">
                                <select name="status" id="status" lay-filter="department" lay-search="">
                                    <option value="">全部</option>
                                    <option value="1">正常</option>
                                    <option value="-1">禁用</option>


                                </select>
                            </div>
                        </div>

                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <button class="layui-btn search" type="button"><i class="layui-icon">&#xe615;</i>查询
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-col-md3">
                <button class="layui-btn" id="add_button">
                    <i class="layui-icon">&#xe654;</i>新增
                </button>
            </div>
        </div>
    </div>

    <!--更改样式-->
    <table id="demo" lay-filter="demoTable" class="layui-table"></table>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="role">角色</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script>

        layui.use(['table', 'laydate','form'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery,form = layui.form;
            table.render({
                elem: '#demo',
                url: '{{url("user/ajaxList")}}?_token={{csrf_token()}}&role_id={{request()->get("role_id")}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 30,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    {field: 'id', title: '用户Id', sort: true, fixed: 'left'}
                    , {field: 'username', title: '用户名'}
                    , {field: 'department', title: '部门'}
                    , {field: 'role', title: '角色'}
                    , {field: 'email', title: '邮箱'}
                    , {field: 'phoneNumber', title: '手机号'}
                    , {field: 'created_at', title: '创建时间'}
                    , {field: 'status', title: '状态',style:'color:blue',event:'setStatus'}
                    , {field: 'updated_at', title: '修改时间'}

                    , {fixed: 'right', align: 'center', toolbar: '#barDemo',title:'操作',width:'150'}
                ]]
            });
            ///排序
            table.on('sort(demoTable)', function (obj) {
                table.reload('demo_reload', {
                    initSort: obj
                    , where: {
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                        , id: $("#id").val()
                    }
                });
            });
            laydate.render({
                elem: '#updated_at'
                , range: '~'
            });
            //查询
            $('.search').click(function () {
                table.reload('demo_reload', {
                    where: {
                        username: $("#username").val(),
                        email: $("#email").val(),
                        phoneNumber: $("#phoneNumber").val(),
                        updated_at: $("#updated_at").val(),
                        status: $("#status").val(),
                        role: $("#role").val(),


                    }
                });
            });
            //编辑
            table.on('tool(demoTable)', function (obj) {
                var data = obj.data;
                if (obj.event === 'edit') {
                    layer.open({
                        title:'编辑',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['60%', '80%'],
                        maxmin: true,
                        content: '{{url('admin/edituser')}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
                    });
                }else if (obj.event === 'role') {
                    layer.open({
                        title:'角色',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['60%', '80%'],
                        maxmin: true,
                        content: '{{url('admin/permission')}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
                    });
                }else if (obj.event === 'del') {
                    layer.confirm('确认删除该用户，删除后该账户不可恢复，删除后该用户的操作日志仍会保留', function (index) {
                        $.post("{{url('user/del')}}/" + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id), '_token={{csrf_token()}}', function (data) {
                            if (data.code == 200) {
                                layer.msg(data.msg, {time: 1000, icon: 1});
                                obj.del();
                                layer.close(index);
                            }
                            else {
                                layer.msg(data.msg, {time: 1000, icon: 5});
                            }
                        }, "json");
                    });
                }
                else if(obj.event === 'setStatus'){
                    if(data.status == '正常'){
                        var operate = '确认将该账户的状态切换为<a style="color: red">禁用</a>？禁用该账号无法登录！';
                    }else{
                        var operate = '确认将该账户的状态切换为<a style="color: blue">正常</a>？';
                    }

                    layer.confirm(operate, function (index) {
                        layer.close(index);
                        $.get("{{url('user/status')}}/" + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id), '_token={{csrf_token()}}', function (data) {
                            if (data.code == 200) {
                                layer.msg(data.msg, {time: 1000, icon: 1});

                                // window.location.reload();
                                $(".layui-laypage-btn").click()
                            }
                            else {
                            }
                        }, "json");
                    });
                }
            });
            //新建
            $('#add_button').click(function(){
                layer.open({
                    type: 2,
                    title: '新建渠道',
                    skin: 'layui-layer-molv',
                    area: ['60%', '80%'],
                    maxmin: true,
                    content: '{{url('admin/createuser')}}?_token={{csrf_token()}}'
                });
            });

            form.render();
        });
    </script>
@endsection