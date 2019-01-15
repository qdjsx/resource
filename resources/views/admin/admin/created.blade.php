@extends('list')
@section('body_content')
    <form class="layui-form" id="form_add">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">用户名:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="username" id="username" lay-verify="required" placeholder="用户名" autocomplete="off" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">所属部门:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <select name="department" lay-filter="department">
                    <option value="">请选择</option>
                    <option value="产品部">产品部</option>
                    <option value="运营部">运营部</option>
                    <option value="技术部">技术部</option>
                    <option value="市场部">市场部</option>
                    <option value="财务部">财务部</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">角色:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <select name="roles" lay-filter="department">
                    <option value="">请选择</option>
                    @foreach($roles as $role)
                    <option value={{$role->id}} name="role">{{$role->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item" >
            <label class="layui-form-label" style="width: 150px;">邮箱:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="email" id="email" lay-verify="required" placeholder="邮箱" autocomplete="off" class="layui-input" type="email">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">手机号:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input name="phone" id="phone" lay-verify="required" placeholder="手机号" autocomplete="off" class="layui-input" type="tel">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">状态:</label>
            <div class="layui-input-block" style="margin-left: 180px;">
                <input type="radio" name="status"  value="1" title="正常" checked="">
                <input type="radio" name="status"  value="-1" title="禁用">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">密码:</label>
            <div class="layui-input-block" style="margin-left: 180px;">

                <input name="password" id="password" lay-verify="" placeholder="" autocomplete="off" class="layui-input" type="password">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px;">确认密码:</label>
            <div class="layui-input-block" style="margin-left: 180px;">

                <input name="sub_password" id="sub_password" lay-verify="" placeholder="" autocomplete="off" class="layui-input" type="password">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>
        layui.use(['form'], function(){
            var form = layui.form;
            //
            form.on('submit(submit)', function(data){
                $.post("{{url('user/save')}}",$("#form_add").serialize(),function(data) {
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
                        parent.location.reload();
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