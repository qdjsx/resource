@extends('list')

@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-mid">权限名称：</label>
                            <div class="layui-input-inline">
                                <input type="tel" name="title" id="title" autocomplete="off" class="layui-input">
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
    </script>

    <script>

        layui.use(['table', 'laydate'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery;
            table.render({
                elem: '#demo',
                url: '{{url("permission/ajaxList")}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 30,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    {field: 'id', title: 'ID', sort: true, fixed: 'left',width:100}
                    , {field: 'name', title: '权限名称',minWidth:200}
                    , {field: 'menu_id', title: '对应菜单'}
                    , {field: 'function', title: '功能'}

                    // , {field: 'created_at', title: '创建时间'}
                    // , {field: 'updated_at', title: '修改时间'}
                    , {field: 'controller', title: '控制器',}
                    , {field: 'action', title: 'action'}
                    , {field: 'is_check', title: '是否匹配参数'}
                    , {field: 'params', title: '参数'}
                    , {align: 'left', toolbar: '#barDemo',title:'操作'}
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
                    ,page: {
                        curr: 1 //重新从第 1 页开始
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
                        content: '{{url('permission/edit')}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
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
                    content: '{{url('permission/create')}}?_token={{csrf_token()}}'
                });
            });

        });
    </script>
@endsection
