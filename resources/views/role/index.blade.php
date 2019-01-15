@extends('list')

@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-mid">角色名称：</label>
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
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script>

        layui.use(['table', 'laydate','element'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery,element = layui.element;


            table.render({
                elem: '#demo',
                url: '{{url("role/ajaxList")}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 30,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    {field: 'id', title: 'ID', sort: true, fixed: 'left'}
                    , {field: 'name', title: '角色名称'}
                    , {field: 'display_name', title: '角色描述'}
                    , {field: 'count',title: '用户数',style:'color:blue',event:'users',}
                    , {field: 'created_at', title: '创建时间',}
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
                        , created_at:$("#created_at").val()
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
                        title: '编辑',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['100%', '100%'],
                        maxmin: true,
                        content: '{{url('role/edited')}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
                    });
                }else if (obj.event === 'del')
                {
                    layer.confirm('确定删除？删除后该角色所对应的用户将不再有此角色权限,此操作不可恢复！', function (index) {
                        $.post("{{url('role/del')}}/" + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id), '_token={{csrf_token()}}', function (data) {
                            if (data.code == 200) {
                                layer.msg(data.msg, {time: 1000, icon: 1});
                                obj.del();
                                layer.close(index);
                                location.reload();
                            }
                            else {
                                layer.msg(data.msg, {time: 1000, icon: 5});
                            }
                        }, "json");
                    });
                }
                else if (obj.event === 'users') {
                    
                }
            });
            //新建
            $('#add_button').click(function(){
                layer.open({
                    type: 2,
                    title: '新建角色',
                    skin: 'layui-layer-molv',
                    area: ['100%', '100%'],
                    maxmin: true,
                    content: '{{url('role/create')}}?_token={{csrf_token()}}'
                });
            });

        });
    </script>
@endsection

