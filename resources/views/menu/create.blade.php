@extends('list')

@section('body_content')
    <form class="layui-form" id="form_edit">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">菜单名称:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="name" id="title" lay-verify="required" placeholder="菜单名称" autocomplete="off"
                       class="layui-input" type="text" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">菜单地址:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="url" id="title" lay-verify="required" placeholder="菜单地址" autocomplete="off"
                       class="layui-input" type="text" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">父id:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <div class="layui-input-inline">
                    <select name="parent_id" lay-verify="required" lay-search="" lay-filter="template">
                        <option value="0">直接选择或搜索选择</option>
                        @foreach ($parents as  $v)
                            <option value="{{$v->id}}">{{$v->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">菜单级别:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <div class="layui-input-inline">
                    <select name="level" lay-verify="required" lay-search="" lay-filter="template">
                        <option value="0">直接选择或搜索选择</option>
                        @foreach ($levelArr as  $k =>$v)
                            <option value="{{$k}}">{{$v}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div c6lass="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>
        layui.use(['form'], function(){
            var form = layui.form,jq = layui.jquery;

            //监听提交
            form.on('submit(submit)', function(data){
                $.post("{{url('menu/store')}}",$("#form_edit").serialize(),function(data) {
                    layer.load();
                    if(data.code == 200) {
                        layer.closeAll('loading');
                        layer.msg(data.msg, {
                            icon:1
                            ,time: 500 //不自动关闭
                            //,btn: ['关闭', '继续操作']
                            ,yes: function(index){
                                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                setTimeout(function () {
                                    parent.layer.close(index); // 关闭layer
                                },1000);
                            }
                        });
                    }
                    else {
                        layer.closeAll('loading');
                        layer.msg(data.msg, {time: 3000, icon:2});
                    }
                },"json");

                return false;
            });

            jq('.pre_btn2').click(function(){
                layer.open({
                    type: 2,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['80%', '80%'], //宽高
                    content: ['{{url('role/permission')}}/0']//iframe的url
                });
            });
        });
    </script>

@endsection