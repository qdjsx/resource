@extends('dialogue')
@section('body_content')

    <form class="layui-form"  id="form_add">
        <div class="layui-form-item">
            <label class="layui-form-label">推广渠道:</label>
            <div class="layui-input-block">
                <select name="channel_id" id="channel_id" lay-verify="required" lay-search="">
                    <option value="">直接选择或搜索选择</option>
                    @foreach($channels as  $channel)
                        <option value="{{$channel->id}}">{{$channel->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">推广代金券:</label>
            <div class="layui-input-block">
                <select name="coupon_id" id="coupon_id" lay-verify="required" lay-search="">
                    <option value="">直接选择或搜索选择</option>
                    @foreach($coupons as  $coupon)
                        <option value="{{$coupon->id}}">{{$coupon->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权重</label>
            <div class="layui-input-block">
                <input type="text" name="weight" lay-verify="number" placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">状态-默认开</label>
            <div class="layui-input-block">
                    <input type="checkbox" name="open" checked="" lay-skin="switch" lay-filter="switchTest"
                           lay-text="ON|OFF">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" class="layui-textarea" name="remark" id="remark"></textarea>
            </div>
        </div>
        <input type="hidden" name="status" id="status" value="1">
        <input type= "hidden"  name="_token" id="_token" value="{{csrf_token()}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    <script>
        layui.use(['form'], function(){
            var form = layui.form,$ = layui.jquery;
            //监听提交
            form.on('submit(submit)', function(data){
                $.post("{{url('crecommend/save')}}",$("#form_add").serialize(),function(data) {
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