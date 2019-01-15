@extends('dialogue')
@section('body_content')

    <form class="layui-form"  id="form_add">
        @foreach ($columns as $column)
            <div class="layui-form-item">
                <label class="layui-form-label">{{$column['title']}}:</label>
                <?php
                    $type = $column['field'];
                ?>
                <div class="layui-input-block">
                    @if($column['type'] == 'text')
                        <input type="text"  name="{{$column['field']}}"  lay-verify="required"  class="layui-input" value="{{$item->$type}}" @if(!empty($column['readonly'])) readonly="readonly" @endif>
                    @elseif($column['type'] == 'select')
                        <select name="{{$column['field']}}" id="{{$column['field']}}" lay-verify="required" lay-search="" @if(!empty($column['readonly'])) disabled="disabled" @endif>
                            <option value="">全部</option>
                            @foreach ($column['values'] as $k =>$v)
                                <option value="{{$k}}" @if ($item->$type == $k) selected="selected" @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    @elseif ($column['type'] == 'file')
                        <button type="button" class="layui-btn layui-btn-danger" id="{{$column['field']}}" name="{{$column['field']}}"><i class="layui-icon"></i>上传图片</button>
                        <div class="layui-inline layui-word-aux">
                            {{$column['size']}}
                        </div>
                    @elseif ($column['type'] == 'radio')
                        <input type="radio" name="add_params" value="1" title="是" @if ($item->add_params == 1) checked="checked"@endif>
                        <input type="radio" name="add_params" value="0" title="否" @if ($item->add_params == 0) checked="checked"@endif>
                    @endif
                </div>
            </div>
        @endforeach
        <input type="hidden" name="image_path" id="image_path" lay-verify="image_path" value="{{$item->image_path}}">
        <input type= "hidden"  name="_token" id="_token" value="{{csrf_token()}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>
        layui.use(['form','upload'], function(){
            var form = layui.form,$ = layui.jquery,upload = layui.upload;
            upload.render({
                elem: '#navigation_image'
                ,url: '{{url("upload")}}?_token={{csrf_token()}}'
                ,accept: 'images'
                ,exts: 'jpg|png|gif|jpeg'
                ,multiple:false
                ,number:1
                ,size: 1000 //限制文件大小，单位 KB
                ,done: function(res){
                    layer.closeAll('loading'); //关闭loading
                    if (res.code == 200) {
                        $("#"+res.index).val(res.path);
                        layer.msg(res.msg, {time: 3000, icon:6});
                    }else{
                        layer.msg(res.msg, {time: 3000, icon:2});
                    }
                }
                ,error: function(index, upload){
                    layer.closeAll('loading');
                }
            });
            //监听提交
            form.on('submit(submit)', function(data){
                $.post("{{url($url)}}/{{$item->id}}/{{md5(env('APP_KEY').$item->id)}}",$("#form_add").serialize(),function(data) {
                    layer.load();
                    if(data.code == 200) {
                        layer.closeAll('loading');
                        layer.msg(data.msg, {
                            icon:1
                            ,time: 0 //不自动关闭
                            ,btn: ['关闭', '继续操作']
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
            form.on('switch(switchTest)', function(data){
                var status = this.checked ? '1' : '-1';
                $("#status").val(status);
            });
        });
    </script>

@endsection