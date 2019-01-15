@extends('list')
@section('body_content')
    <form class="layui-form layui-form-pane" id="form_update" style="margin-left: 30px;margin-top: 20px">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="layui-form-item">
            <label class="layui-form-label">用户名：</label>
            <div class="layui-input-block" >
                <input type="text" name="username"   lay-verify="required" value="{{$admin->username}}" autocomplete="off" class="layui-input" style="width:200px; text-align: center" disabled="disabled" >
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">所属部门：</label>
            <div class="layui-input-block">
                <label class="layui-form-label" style="width:200px;">{{$admin->department}}</label>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">角色：</label>
            <div class="layui-input-block" >
                <label class="layui-form-label" style="width:200px;">@foreach($admin->roles as $role){{$role->name}}@endforeach</label>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">邮箱：</label>
            <div class="layui-input-block" >
                <input type="email" name="email"   lay-verify="required" value="{{$admin->email}}" autocomplete="off" class="layui-input" style="width:200px;text-align: center">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">手机号：</label>
            <div class="layui-input-block" >
                <input type="tel" name="phone"   lay-verify="required" value="{{$admin->phone}}" autocomplete="off" class="layui-input" style="width:200px;text-align: center" >
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态：</label>
            <div class="layui-input-block" >
                <label class="layui-form-label" style="width:200px">{!!$admin->status ==1?'正常':'<a style="color: red">禁用</a>'!!}</label>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">修改密码：</label>
            <div class="layui-input-block" >
                <input type="password" name="password"   lay-verify="required"  autocomplete="off" class="layui-input" style="width:200px;text-align: center">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">确认密码：</label>
            <div class="layui-input-block" >
                <input type="password" name="sub_password"   lay-verify="required"  autocomplete="off" class="layui-input" style="width:200px;text-align: center">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>
        layui.use(['form','upload'], function(){
            var form = layui.form,jq =layui.jquery;
            //
            form.on('submit(submit)', function(data){
                $.post("{{url('personal/update')}}/{{$admin->id}}/{{csrf_token()}}",$("#form_update").serialize(),function(data) {
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
                        parent.layer.msg(data.msg, {time: 3000, icon:2});

                    }

                },"json");
                return false;

            });



        });
    </script>
@endsection
