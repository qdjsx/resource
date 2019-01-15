@extends('list')
@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-mid">邮箱：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="title" id="title" autocomplete="off" class="layui-input">
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
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">角色</a>
    </script>

    <script>

        layui.use(['table', 'laydate'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery;
            table.render({
                elem: '#demo',
                url: '{{url("user/ajaxList")}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 10,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    {field: 'id', title: '用户Id', sort: true, fixed: 'left'}
                    , {field: 'email', title: '邮箱'}
                    , {field: 'created_at', title: '创建时间'}
                    , {field: 'updated_at', title: '修改时间'}
                    , {fixed: 'right', align: 'center', toolbar: '#barDemo',title:'操作'}
                ]]
            });
            ///排序
            table.on('sort(demoTable)', function (obj) {
                table.reload('demo_reload', {
                    initSort: obj
                    , where: {
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                        , title: $("#title").val()
                    }
                });
            });
            laydate.render({
                elem: '#reg_time'
                , range: '~'
            });
            //查询
            $('.search').click(function () {
                table.reload('demo_reload', {
                    where: {
                        title: $("#title").val()
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
                }else if (obj.event === 'del') {
                    layer.open({
                        title:'角色',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['60%', '80%'],
                        maxmin: true,
                        content: '{{url('admin/permission')}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
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

        });
    </script>
@endsection