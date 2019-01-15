@extends('list')
@section('body_content')
    <div class="layui-col-md12">
        <div class="layui-row grid-demo">
            <div class="layui-form-query">
                <form class="layui-form" id="query_form">
                    <div class="layui-form-item">
                        @foreach ($head['select'] as $v)
                        <div class="layui-inline">
                            <label class="layui-form-mid">{{$v['desc']}}：</label>
                            <div class="layui-input-inline">
                                @if ($v['type'] == 'text')
                                    <input type="{{$v['type']}}" name="{{$v['name']}}" id="{{$v['name']}}" autocomplete="off" class="layui-input">
                                @elseif ($v['type'] == 'select')
                                    <select name="{{$v['name']}}" id="{{$v['name']}}" lay-verify="required" lay-search="">
                                            <option value="0">全部</option>
                                        @foreach ($v['values'] as $k =>$item)
                                            <option value="{{$k}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                    @elseif ($v['type'] == 'time_reg')
                                    <input type="text" class="layui-input" id="{{$v['name']}}" name="{{$v['name']}}" daterange = "1" placeholder="~">
                                @endif
                            </div>
                        </div>
                        @endforeach
                        @if (!empty($head['search']))
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <button class="layui-btn search" type="button"><i class="layui-icon">&#xe615;</i>查询
                                </button>
                            </div>
                            @if (!empty($exces['download']))
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-warm download" type="button"><i class="layui-icon">&#xe601;</i>导出
                                    </button>
                                </div>
                            @endif
                        </div>
                        @endif

                    </div>
                </form>
            </div>
            @if(!empty($routeArr['create']))
            <div class="layui-col-md3">
                <button class="layui-btn" id="add_button">
                    <i class="layui-icon">&#xe654;</i>新增
                </button>
            </div>
            @endif
            @if (!empty($exces['leadingin']))
                {{--<button type="button" class="layui-btn layui-btn-warm" id="test1">--}}
                    {{--<i class="layui-icon">&#xe62f;</i>导入--}}
                {{--</button>--}}
            @endif
        </div>
    </div>
    <table id="demo" lay-filter="demoTable" class="layui-table"></table>
    <script type="text/html" id="barDemo">
    @if (!empty($toolBar['edit'])) <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a> @endif
    @if (!empty($toolBar['delete'])) <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a> @endif
    </script>

    <script>

        layui.use(['table','laydate'], function () {
            var table = layui.table, $ = layui.jquery,laydate = layui.laydate;
            laydate.render({
                elem: '#updated_at'
                ,range: '~'
            });
            table.render({
                elem: '#demo',
                url: '{{url($ajaxUrl)}}?_token={{csrf_token()}}',
                method: 'post',
                loading: true,
                skin: 'line',
                page: true,
                limit: 10,
                id: 'demo_reload',
                sortType: "remote", //默认: "local"或空
                cols: [[ //标题栏
                    @foreach($columns as $column)
                    {field: '{{$column["field"]}}', title: '{{$column['title']}}', sort: @if ($column['sort'])  true @else false @endif},
                    @endforeach
                    {fixed: 'right', align: 'center', toolbar: '#barDemo',title:'操作'}
                ]]
            });
            table.on('sort(demoTable)', function (obj) {
                table.reload('demo_reload', {
                    initSort: obj
                    , where: {
                        field: obj.field //排序字段
                        , order: obj.type //排序方式
                        @foreach($head['select'] as $select)
                            ,{{$select['name']}} : $("#{{$select['name']}}").val()
                        @endforeach
                    }
                });
            });

            $('.search').click(function () {
                table.reload('demo_reload', {
                    where: {
                    @foreach($head['select'] as $select)
                        {{$select['name']}} : $("#{{$select['name']}}").val(),
                    @endforeach
                    }


                });
            });
            table.on('tool(demoTable)', function (obj) {
                var data = obj.data;
               if (obj.event === 'edit') {
                    layer.open({
                        title:  '编辑',
                        type: 2,
                        skin: 'layui-layer-molv',
                        area: ['60%', '80%'],
                        maxmin: true,
                        content: '{{url($routeArr['edit'])}}/' + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id)
                    });
                }else if (obj.event === 'del') {
                   layer.confirm('真的删除行么', function (index) {
                       $.post("{{url($routeArr['delete'])}}/" + data.id + '/' + $.md5("{{(env('APP_KEY'))}}" + data.id), '_token={{csrf_token()}}', function (data) {
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
            @if(!empty($routeArr['create']))
            $('#add_button').click(function(){
                layer.open({
                    type: 2,
                    title: '新建',
                    skin: 'layui-layer-molv',
                    area: ['60%', '80%'],
                    maxmin: true,
                    content: '{{url($routeArr['create'])}}?_token={{csrf_token()}}'
                });
            });
            @endif
            @if (!empty($exces['download']))
            $('.download').click(function () {
                window.location.href= "{{url('flux/download')}}?operator="+$("#operator").val()+"&range="+$("#range").val()+"&status="+$("#status").val()+"&is_special="+$("#is_special").val()+"&updated_at="+$("#updated_at").val()+"&title="+$("#title").val();
            });
            @endif
            @if (!empty($exces['leadingin']))
            layui.use('upload', function(){
                var upload = layui.upload;
                //执行实例
                upload.render({
                    elem: '#test1' //绑定元素
                    ,url: "{{url('flux/leadingin')}}?_token={{csrf_token()}}" //上传接口
                    ,accept: 'file'
                    ,exts: 'xls|xlsx'
                    ,method: 'post'
                    ,done: function(data){
                        //上传完毕回调
                            layer.msg(data.msg, {time: 1000, icon: 1});
                            layer.close(index);
                    }
                    ,error: function(){
                        //请求异常回调
                    }
                });
            });
            @endif

        });
    </script>
@endsection