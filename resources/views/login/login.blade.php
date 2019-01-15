<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="" />
    <title>懒人签到后台管理中心</title>
    <link rel="stylesheet" href="{{url('login/css/style.css')}}" />
    <script src="{{url('layui/layui.js')}}" charset="utf-8"></script>
    <script src="{{url('js/jquery-2.1.1.js')}}"></script>
</head>
<body>
<div class="login">
    <h2>登陆</h2>
    <div class="login-top">
        <h1>欢迎登陆</h1>
        <form id="form_add">
            <input type="text" name="email" id="email"  placeholder="账号">
            <input type="password" name="password" id="password" placeholder="密码">
            {{--<div class="forgot">--}}
                {{--<input type="text" style="width: 150px;" name="phone_code" id="phone_code"   placeholder="手机验证码">--}}
                {{--<a href='javascript:;'   id="getcode" onclick="getcode()">--}}
                    {{--获取验证码--}}
                {{--</a>--}}
            {{--</div>--}}
            <div class="forgot">
                <input type="submit" value="提交" lay-submit="submit" lay-filter="submit">
            </div>
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        </form>
    </div>
    <div class="login-bottom">
    </div>
</div>
<div class="copyright">
    <p>
        Copyright &copy; 2017.懒人互动 All rights reserved<a target="_blank" href="http://www.cssmoban.com/"></a>
    </p>
</div>

<script>
    console.log("{{$ip}}");
    function getcode()
    {
        var email = $("#email").val();
        if (email == '' || email.length == 0){
            layer.msg('账号不能为空');
            return false;
        }
        $.post("{{url('admin/needcode')}}",$("#form_add").serialize(),function(data) {
            if(data.error_code == 200) {
                $('#getcode').text('60秒后重新获取');
                $('#getcode').removeAttr('onclick');
                //写个定时修改文本settime
                var time = 59;
                var into = setInterval(function(){
                    $('#getcode').text(time+'秒后重新获取');
                    time =time -1;
                    if(time<=-1){
                        clearInterval(into);
                        $('#getcode').text('获取验证码');
                        $('#getcode').attr('onclick',"getcode()");
                    }
                },1000);
            }
            else {
                layer.msg(data.msg);
            }

        },"json");

        return false;
    }
</script>

<script>
    layui.use(['form','layer'], function() {
        var form = layui.form,jq = layui.jquery,layer = layui.layer;
        form.on('submit(needcode)', function(data) {
            var email = jq("#email").val();
            if (email == '' || email.length == 0){
                layer.msg('账号不能为空');
                return false;
            }
            jq.post("{{url('admin/needcode')}}",jq("#form_add").serialize(),function(data) {
                if(data.error_code == 200) {
                    layer.msg(data.msg);
                }
                else {
                    layer.msg(data.msg);
                }

            },"json");

            return false;
        });

        form.on('submit(submit)', function(data) {
            var email = jq("#email").val(),password = jq("#password").val(),phone = jq("#phone_code").val();
            if (email == '' || email.length == 0){
                layer.msg('账号不能为空');
                return false;
            }
            if (password == '' || password.length == 0){
                layer.msg('密码不能为空');
                return false;
            }
            // if (phone == '' || phone.length == 0){
            //     layer.msg('验证码不能为空');
            //     return false;
            // }
            jq.post("{{url('admin/login')}}",jq("#form_add").serialize(),function(data) {
                if(data.error_code == 200) {
                    layer.msg(data.msg);
                    window.location.href ="{{url('admin/panel')}}";
                }
                else {
                    layer.msg(data.msg);
                }

            },"json");

            return false;
        });

    });
</script>
</body>
</html>
