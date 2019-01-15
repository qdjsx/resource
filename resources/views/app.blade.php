<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{env('APP_TITLE')}}</title>
    <link rel="stylesheet" href="{{url('layui/css/layui.css')}}" media="all"/>
    <link rel="stylesheet" href="{{url('plugins/font-awesome.min.css')}}" media="all"/>
    <link rel="stylesheet" href="{{url('plugins/app.css')}}" media="all"/>
    <link rel="stylesheet" href="{{url('plugins/default.css')}}" media="all" id="skin" kit-skin/>
</head>

<body class="kit-theme">
<div class="layui-layout layui-layout-admin kit-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">懒人资源管理后台</div>
        <div class="layui-logo kit-logo-mobile">K</div>
        <ul class="layui-nav layui-layout-right kit-nav">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <i class="layui-icon">&#xe63f;</i> 皮肤</a>
                </a>
                <dl class="layui-nav-child skin">
                    <dd><a href="javascript:;" data-skin="default" style="color:#393D49;"><i
                                    class="layui-icon">&#xe658;</i> 默认</a></dd>
                    <dd><a href="javascript:;" data-skin="orange" style="color:#ff6700;"><i
                                    class="layui-icon">&#xe658;</i> 橘子橙</a></dd>
                    <dd><a href="javascript:;" data-skin="green" style="color:#00a65a;"><i
                                    class="layui-icon">&#xe658;</i> 原谅绿</a></dd>
                    <dd><a href="javascript:;" data-skin="pink" style="color:#FA6086;"><i
                                    class="layui-icon">&#xe658;</i> 少女粉</a></dd>
                    <dd><a href="javascript:;" data-skin="blue.1" style="color:#00c0ef;"><i
                                    class="layui-icon">&#xe658;</i> 天空蓝</a></dd>
                    <dd><a href="javascript:;" data-skin="red" style="color:#dd4b39;"><i class="layui-icon">&#xe658;</i>
                            枫叶红</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="http://m.zhengjinfan.cn/images/0.jpg" class="layui-nav-img"> {{$item->username}}
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="javascript:;" kit-target
                           data-options="{url:'{{url('personal/index')}}',icon:'&#xe612;',title:'个人中心'}"><span>个人中心</span></a>
                    </dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="{{url('auth/logout')}}"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black kit-side">
        <div class="layui-side-scroll">
            <div class="kit-side-fold"><i class="fa fa-navicon" aria-hidden="true"></i></div>
            <ul class="layui-nav layui-nav-tree" lay-filter="kitNavbar" kit-navbar>
                <?php $i = 0;?>
                @foreach(Session::get('menu') as $k => $v)
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;"><i class="layui-icon" aria-hidden="true">&#xe654;</i><span>{{$v['title']}}</span></a>
                        <dl class="layui-nav-child">
                            @if(!empty($v['sub_menu']))
                                @foreach ($v['sub_menu'] as $subK => $subV)
                                    <dd>
                                        <a href="javascript:;" kit-target
                                           data-options="{url:'{{url($subV['uri'])}}',icon:'{{$subV['icon']}}',title:'{{$subV['title']}}',id:'{{$i++}}'}">
                                            <i class="layui-icon">{{$subV['icon']}}</i><span>{{$subV['title']}}</span></a>
                                    </dd>

                                 @endforeach
                            @endif
                            {{--三级菜单--}}
                            @if(!empty($v['three_class']))
                                    @foreach($v['three_class'] as $three)
                                                <a href="javascript:;" class="menu_three" ><i class="layui-icon" aria-hidden="true">&#xe654;</i><span>{{$three['title']}}</span></a>
                                                <ol class="layui-nav-child" style="display: none;" >
                                                @foreach ($three['sub_menu'] as $subK => $subV)
                                                    <li>
                                                    <a href="javascript:;" kit-target
                                                   data-options="{url:'{{url($subV['uri'])}}',icon:'{{$subV['icon']}}',title:'{{$subV['title']}}',id:'{{$i++}}'}">
                                                    <i class="layui-icon" style="margin-left: 20px">&#xe65f;{{$subV['icon']}}</i><span>{{$subV['title']}}</span></a>
                                                    </li>
                                                @endforeach
                                                    </ol>
                                    @endforeach

                            @endif
                                {{--三级菜单--}}
                        </dl>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="layui-body" id="container">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;"><i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop">&#xe63e;</i>
            请稍等...
        </div>
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        <span style="text-align: center;">{{date('Y')}} &copy;
            <a href="#">北京懒人互动科技有限公司</a>
        </span>
    </div>
</div>
<script src="{{url('layui/layui.js')}}"></script>
<script src="{{url('js/jquery-2.1.1.js')}}"></script>
<script>
    var message;
    layui.config({
        base: '{{url("js")}}/',
        version: '1.0.1'
    }).use(['app', 'message'], function () {
        var app = layui.app,
            $ = layui.jquery,
            layer = layui.layer;
        //将message设置为全局以便子页面调用
        message = layui.message;
        //主入口
        app.set({
            type: 'iframe'
        }).init();
        $('#pay').on('click', function () {
            layer.open({
                title: false,
                type: 1,
                content: '<img src="/build/images/pay.png" />',
                area: ['500px', '250px'],
                shadeClose: true
            });
        });
        $('dl.skin > dd').on('click', function () {
            var $that = $(this);
            var skin = $that.children('a').data('skin');
            switchSkin(skin);
        });
        var setSkin = function (value) {
                layui.data('kit_skin', {
                    key: 'skin',
                    value: value
                });
            },
            getSkinName = function () {
                return layui.data('kit_skin').skin;
            },
            switchSkin = function (value) {
                var _target = $('link[kit-skin]')[0];
                _target.href = _target.href.substring(0, _target.href.lastIndexOf('/') + 1) + value + _target.href.substring(_target.href.lastIndexOf('.'));
                setSkin(value);
            },
            initSkin = function () {
                var skin = getSkinName();
                switchSkin(skin === undefined ? 'default' : skin);
            }();
    });


    layui.use(['element','jquery'], function(){
        var element = layui.element,$=layui.jquery;
        $(".menu_three").on("click",function(){
            $(this).next().toggle();
            $.each($(this).parent().siblings(), function (i, e) {
                $(e).find("ol").hide();
            });
        });
        $("ol").on("click","li a",function(){
            $.each($(this).parent().siblings(),function(i,e){
                $(e).find("a").removeClass('three_this')
            });
            $(this).addClass('three_this');                            // 添加当前元素的样式
        })
    });
</script>
</body>

</html>