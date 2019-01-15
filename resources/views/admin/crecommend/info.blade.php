@extends('dialogue')
@section('body_content')

<form class="layui-form">
    <div class="layui-form-item">
        <label class="layui-form-label">标题:</label>
        <div class="layui-input-block">
            <input type="text"  readonly="readonly" value="{{$item->title}}"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">副标题:</label>
        <div class="layui-input-block">
            <input type="text"  readonly="readonly" value="{{$item->sub_title}}"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">类型:</label>
        <div class="layui-input-block">
            <select name="type" id="type" lay-verify="required" lay-search="">
                <option value="0">直接选择或搜索选择</option>
                @foreach($typeArr as  $key => $v)
                    <option value="{{$key}}" @if($item->type == $key) selected @endif>{{$v}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">所属媒体:</label>
        <div class="layui-input-block">
            <select name="channel_id" id="channel_id" lay-verify="required" lay-search="">
                <option value="0">直接选择或搜索选择</option>
                @foreach($channels as  $channel)
                    <option value="{{$channel->id}}" @if ($item->channel_id == $key) selected @endif>{{$channel->title}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">原价:</label>
        <div class="layui-input-block">
            <input type="text"  readonly="readonly" value="{{$item->price}}"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">售价:</label>
        <div class="layui-input-block">
            <input type="text"  readonly="readonly" value="{{$item->needs_gold}}"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">投放黑名单:</label>
        <div class="layui-input-block">
            <select name="black_channel_id" id="black_channel_id" lay-verify="required" lay-search="">
                <option value="0">直接选择或搜索选择</option>
                @foreach($channels as  $channel)
                    <option value="{{$channel->id}}" @if ($item->black_channel_id == $key) selected @endif>{{$channel->title}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">定向渠道:</label>
        <div class="layui-input-block">
            <select name="orientation_channel_id" id="orientation_channel_id" lay-verify="required" lay-search="">
                <option value="0">直接选择或搜索选择</option>
                @foreach($channels as  $channel)
                    <option value="{{$channel->id}}" @if ($item->orientation_channel_id == $key) selected @endif>{{$channel->title}}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>
<script>
    layui.use(['form'], function(){
        var form = layui.form
    });
</script>

@endsection