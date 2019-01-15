@extends('list')
@section('body_content')
	<form class="layui-form" id="form_add">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="layui-form-item">
			<label class="layui-form-label" style="width: 150px;">邮箱:</label>
			<div class="layui-input-block" style="margin-left: 180px;">
				<input name="email" id="email" lay-verify="required" placeholder="邮箱" autocomplete="off" class="layui-input" type="email">
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
                $.post("{{url('admin/save')}}",$("#form_add").serialize(),function(data) {
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