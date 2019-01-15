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
    <link rel="stylesheet" href="{{url('plugins/fs.css')}}" media="all" id="skin" kit-skin/>
</head>
<body>
<script src="{{url('layui/layui.js')}}" charset="utf-8"></script>
<script src="{{url('js/jquery-2.1.1.js')}}" charset="utf-8"></script>
<script src="{{url('js/jquery.md5.js')}}" charset="utf-8"></script>
<script src="{{url('js/publicSelf.js')}}" charset="utf-8"></script>
@yield('body_content')

</body>
</html>