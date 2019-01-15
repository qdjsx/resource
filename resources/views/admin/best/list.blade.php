@extends('list')
@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-mid">渠道：</label>
                            <div class="layui-input-inline">
                                <select name="channel_id" id="channel_id" lay-verify="required" lay-search="">
                                    <option value="0">直接选择或搜索选择</option>
                                    @foreach($channels as  $channel)
                                        <option value="{{$channel->id}}">{{$channel->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">状态：</label>
                            <div class="layui-input-inline">
                                <select name="status" id="status" lay-verify="required" lay-search="">
                                    <option value="0">直接选择或搜索选择</option>
                                    @foreach($statusArr as  $k => $v)
                                        <option value="{{$k}}">{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">修改时间：</label>
                            <div class="layui-input-inline" style="">
                                <input type="text" name="reg_time" id="reg_time" autocomplete="off"
                                       class="layui-input fsDate"
                                       daterange="1" placeholder="~">
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
    <table id="demo" lay-filter="demoTable" class="layui-table"></table>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script>

        layui.use(['table', 'laydate'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery;
            table.render({
                elem: '#demo',
                url: '{{url("best/ajaxList")}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 10,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    {field: 'goods_id', title: 'ID', sort: false, fixed: 'left'}
                    , {field: 'channel_id', title: '渠道名称'}
                    , {field: 'title', title: '商品名称'}
                    , {field: 'weight', title: '权重'}
                    , {field: 'status', title: '状态'}
                    , {field: 'updated_at', title: '修改时间'}
                    , {fixed: 'right', align: 'center', toolbar: '#barDemo',title:'操作'}
                ]]
            });
            table.on('sort(demoTable)', function (obj) {
                table.reload('demo_reload', {
                    initSort: obj
                    , where: {
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                        , title: $("#title").val()
                        , channel_id: $("#channel_id").val()
                        , status: $("#status").val()
                        , reg_time: $("#reg_time").val()
                    }
                });
            });
            laydate.render({
                elem: '#reg_time'
                , range: '~'
            });
            $('.search').click(function () {
                table.reload('demo_reload', {
                    where: {
                        title: $("#title").val()
                        , channel_id: $("#channel_id").val()
                        , status: $("#status").val()
                        , reg_time: $("#reg_time").val()
                    }
                    ,page: {
                        curr: 1 //重新从第 1 页开始
                    }
                });
            });
            table.on('tool(demoTable)', function (obj) {
                var data = obj.data;
               if (obj.event === 'edit') {
                    layer.open({
                        title: data.title + '-编辑',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['60%', '80%'],
                        maxmin: true,
                        content: '{{url("best/edit")}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
                    });
                }else if (obj.event === 'del') {
                   layer.confirm('真的删除行么', function (index) {
                       $.post("{{url('best/del')}}/" + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id), '_token={{csrf_token()}}', function (data) {
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
            });
            $('#add_button').click(function(){
                layer.open({
                    type: 2,
                    title: '新建商品推荐',
                    skin: 'layui-layer-molv',
                    area: ['60%', '80%'],
                    maxmin: true,
                    content: '{{url('best/create')}}?_token={{csrf_token()}}'
                });
            });

        });
    </script>
@endsection