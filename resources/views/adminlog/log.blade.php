@extends('list')
@section('body_content')
    <div class="layui-col-md12" style="margin-left: 30px;margin-top: 20px">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline ">
                            <label class="layui-form-mid">用户ID：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="admin_id" id="admin_id" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline ">
                            <label class="layui-form-mid">角色：</label>
                            <div class="layui-input-inline">
                                <select name="roles" id="roles" lay-filter="department" lay-search="">
                                    <option value="">全部</option>
                                    @foreach($roles as $role)
                                        <option value={{$role->id}}>{{$role->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="layui-inline ">
                            <label class="layui-form-mid">动作：</label>
                            <div class="layui-input-inline">
                                <select name="action" id="action" lay-search="" lay-filter="department">
                                    <option value="">全部</option>
                                    @foreach($actions as $k => $v)
                                        <option value={{$k}}>{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline ">
                            <label class="layui-form-mid">对象类型：</label>
                            <div class="layui-input-inline">
                                <select name="type" id="type" lay-filter="department" lay-search="">
                                    <option value="">全部</option>
                                    @foreach($types as $type)
                                        <option value={{$type->name}}>{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">操作时间：</label>
                            <div class="layui-input-inline" >
                                <input type="text" name="create_at" id="create_at" autocomplete="off"
                                       class="layui-input fsDate"
                                       daterange="1" placeholder="~">
                            </div>
                        </div>
                        <label class="layui-form-mid">对象名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="operation" id="operation" autocomplete="off"
                                   class="layui-input">
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
        </div>
    </div>

    <table id="demo" lay-filter="demoTable" class="layui-table"></table>

    <script>
        layui.use(['table', 'laydate'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery;
            table.render({
                elem: '#demo',
                url: '{{url("admin/ajaxList")}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 30,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[
                    {field: 'create_at', title: '时间', minWidth: '110', sort: true,width:'15%',align:'center'}
                    , {field: 'admin_id', title: '用户ID',width:'10%',align:'center'}
                    , {field: 'username', title: '用户名',width:'10%',align:'center'}
                    , {field: 'role', title: '角色',width:'10%' ,align:'center'}
                    , {field: 'action', title: '动作', width:'10%',align:'center'}
                    , {field: 'type', title: '对象类型',width:'20%',align:'center'}
                    , {field: 'operation', title: '对象名称',width:'25%',align:'center'}
                ]]
            });
            table.on('sort(demoTable)', function (obj) {
                table.reload('demo_reload', {
                    initSort: obj
                    , where: {
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                        , create_at: $("#create_at").val()
                    }
                });
            });
            laydate.render({
                elem: '#create_at'
                , range: '~'
            });
            $('.search').click(function () {
                table.reload('demo_reload', {
                    where: {
                        admin_id: $("#admin_id").val()
                        , create_at: $("#create_at").val()
                        , action: $("#action").val()
                        , operation: $("#operation").val()
                        , roles: $("#roles").val()
                        , type: $("#type").val()
                    }
                    , page: {
                        curr: 1 //重新从第 1 页开始
                    }
                });
            });
        });
    </script>
@endsection