@extends('list')

@section('body_content')
    <form class="layui-form" id="form_edit">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">权限名称:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="name" id="title" lay-verify="required" placeholder="请输入权限名称" autocomplete="off"
                       class="layui-input" type="text" value="">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">对应菜单:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <div class="layui-input-inline">
                    <select name="menu_id" lay-verify="required" lay-search="" lay-filter="template">
                        <option value="0">直接选择或搜索选择</option>
                        @foreach ($parents as  $v)
                            <option value="{{$v->id}}">{{$v->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">功能:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <div class="layui-input-inline">
                    <select name="function" lay-verify="required" lay-search="" lay-filter="template">
                        <option value="0">直接选择或搜索选择</option>
                        @foreach ($function as  $k => $v)
                            <option value="{{$k}}">{{$v}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">权限描述</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <div class="layui-input-inline">
                <input name="display_name" id="limit_times" lay-verify="required" placeholder="请输入权限描述" autocomplete="off"
                       class="layui-input" type="text" value="">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">控制器:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="controller" id="title" lay-verify="required" placeholder="请输入控制器" autocomplete="off"
                       class="layui-input" type="text" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">action:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="action" id="title" lay-verify="required" placeholder="请输入action" autocomplete="off"
                       class="layui-input" type="text" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">是否匹配参数:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input type="radio" name="is_check" value="1" title="是" checked="">
                <input type="radio" name="is_check" value="0" title="否">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">参数:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="params" id="title" lay-verify="" placeholder="" autocomplete="off"
                       class="layui-input" type="text" value="*">
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
            var form = layui.form;
            //
            form.on('submit(submit)', function(data){
                $.post("{{url('permission/store')}}",$("#form_edit").serialize(),function(data) {
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
        });
    </script>

@endsection