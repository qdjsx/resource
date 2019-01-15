@extends('list')
@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-mid">内部标题：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="inside_title" id="inside_title" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">页面标题：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="page_title" id="page_title" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">url后缀：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="landing_page" id="landing_page" autocomplete="off" class="layui-input">
                            </div>
                        </div>


                        <div class="layui-inline">
                            <label class="layui-form-mid">平台：</label>
                            <div class="layui-input-inline">
                                <select name="platform" id="platform" lay-verify="required" lay-search="">
                                    <option value="0">直接选择或搜索选择</option>
                                    {{--@foreach($platform as  $k =>$v)--}}
                                        {{--<option value="{{$k}}">{{$v}}</option>--}}
                                    {{--@endforeach--}}
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">状态：</label>
                            <div class="layui-input-inline">
                                <select name="status" id="status" lay-verify="required" lay-search="">
                                    <option value="0">直接选择或搜索选择</option>
                                    {{--@foreach($statusArr as  $k => $v)--}}
                                        {{--<option value="{{$k}}">{{$v}}</option>--}}
                                    {{--@endforeach--}}
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">开始时间：</label>
                            <div class="layui-input-inline" style="">
                                <input type="text" name="start_date" id="start_date" autocomplete="off"
                                       class="layui-input fsDate"
                                       daterange="1" placeholder="~">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-mid">结束时间：</label>
                            <div class="layui-input-inline" style="">
                                <input type="text" name="end_date" id="end_date" autocomplete="off"
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
    {{--<table id="demo" lay-filter="demoTable" class="layui-table"></table>--}}
    {{--<script type="text/html" id="barDemo">--}}
        {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="copy">复制</a>--}}
        {{--<a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>--}}
        {{--<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="">预览</a>--}}
    {{--</script>--}}

    <script>

        layui.use(['table', 'laydate'], function () {
            var table = layui.table, laydate = layui.laydate, $ = layui.jquery;
            table.render({
                elem: '#demo',
                url: '{{url("template/ajaxList")}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 30,
                limits: [50,100,200],
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    {field: 'id', title: 'ID', sort: true, fixed: 'left'}
                    , {field: 'inside_title', title: '内部标题'}
                    , {field: 'page_title', title: '页面标题'}
                    , {field: 'landing_page', title: 'url'}
                    , {field: 'platform', title: '平台'}
                    , {field: 'status', title: '状态',templet: '#switchTpl', unresize: true}
                    , {field: 'start_date', title: '开始时间'}
                    , {field: 'end_date', title: '结束时间'}
                    , {fixed: 'right', align: 'center', toolbar: '#barDemo',title:'操作',minWidth:'150'}
                ]]
            });
            table.on('sort(demoTable)', function (obj) {
                table.reload('demo_reload', {
                    initSort: obj
                    , where: {
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                        , inside_title: $("#inside_title").val()
                        , page_title: $("#page_title").val()
                        , landing_page: $("#landing_page").val()
                        , platform: $("#platform").val()
                        , status: $("#status").val()
                        , start_date: $("#start_date").val()
                        , end_date: $("#end_date").val()
                    }
                });
            });
            laydate.render({
                elem: '#start_date'
                , range: '~'
            });
            laydate.render({
                elem: '#end_date'
                , range: '~'
            });
            $('.search').click(function () {
                table.reload('demo_reload', {
                    where: {
                        inside_title: $("#inside_title").val()
                        , page_title: $("#page_title").val()
                        , landing_page: $("#landing_page").val()
                        , platform: $("#platform").val()
                        , status: $("#status").val()
                        , start_date: $("#start_date").val()
                        , end_date: $("#end_date").val()
                    }
                    ,page: {
                        curr: 1 //重新从第 1 页开始
                    }
                });
            });
            table.on('tool(demoTable)', function (obj) {
                var data = obj.data;
                if (obj.event === 'copy') {
                    layer.open({
                        title: data.inside_title + '-复制',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['60%', '80%'],
                        maxmin: true,
                        content: '{{url("template/copy")}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id) + '?id=' + data.id
                    });
                }else if (obj.event === 'edit') {
                    layer.open({
                        title: data.inside_title + '-编辑',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['100%', '100%'],
                        maxmin: true,
                        content: '{{url("template/edit")}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id) + '?id=' + data.id
                    });
                }else if (obj.event === 'del') {
                    layer.confirm('真的删除行么', function (index) {
                        $.post("{{url('template/del')}}/" + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id), '_token={{csrf_token()}}', function (data) {
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
                    title: '新建活动',
                    skin: 'layui-layer-molv',
                    area: ['60%', '80%'],
                    maxmin: true,
                    content: '{{url('template/create')}}?_token={{csrf_token()}}'
                });
            });
        });
    </script>
@endsection