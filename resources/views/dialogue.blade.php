<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{env('APP_TITLE')}}</title>
    <link rel="stylesheet" href="{{url('layui/css/layui.css')}}" media="all"/>
</head>
<body>
<script src="{{url('layui/layui.js')}}" charset="utf-8"></script>
<div class="layui-fluid">
    @yield('body_content')
</div>
</body>
</html>