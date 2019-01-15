@extends('list')
@section('body_content')
    <link rel="stylesheet" href="{{url('css/jquery.tree-multiselect.min.css')}}">
    <script src="{{asset('js/jquery-2.1.1.js')}}"></script>
    <script src="{{asset('js/jquery.tree-multiselect.min.js')}}"></script>
    <form class="" id="form_add">
        <select id="test-select-2" multiple="multiple">
                @foreach ($geoArr as $slot)
                    <option value="{{$slot['parent_code']}}" @if (isset($geoCodes[$slot['parent_code']])) selected @endif>{{$slot['name']}}</option>
                @endforeach
        </select>
        </br>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="role_ids" value="{{$roleIds}}" id="role_ids"/>
    </form>
    <script type="text/javascript">
        var tree2 = $("#test-select-2").treeMultiselect({
            enableSelectAll: true,
            allowBatchSelection: true,
            searchable: true,
            startCollapsed: true,
            selectAllText:'全选',
            unselectAllText:'全不选'
        });
        $('select').change(function(){
            var ids = "";
            $('select :selected').each(function() {
                ids += Number($(this).val())+',';
            });
            $("#role_ids").val(ids);
        });

        layui.use(['form'], function(){
            var form = layui.form;
            //
            form.on('submit(submit)', function(data){
                $.post("{{url('admin/updatePermission')}}/{{$item->id}}",$("#form_add").serialize(),function(data) {
                    layer.load();
                    if(data.code == 200) {
                        layer.closeAll('loading');
                        layer.msg(data.msg, {
                            icon:1
                            ,time: 500 //不自动关闭
                           // ,btn: ['关闭', '继续操作']
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
        })
    </script>
@endsection