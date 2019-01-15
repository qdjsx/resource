<link rel="stylesheet" href="{{url('css/jquery.tree-multiselect.min.css')}}">
<script src="{{asset('js/jquery-2.1.1.js')}}"></script>
<script src="{{asset('js/jquery.tree-multiselect.min.js')}}"></script>
<select id="test-select-2" multiple="multiple">
    @foreach($geoArr as $geo)
        @if (!is_array($geo['value']))
            <option value="{{$geo['parent_code']}}" @if(isset($geoCodes[$geo['parent_code']])) selected="selected" @endif >{{$geo['value']}}</option>
        @else
            @foreach ($geo['value'] as $v)
                <option value="{{$v['code']}}" data-section="{{$geo['name']}}"  @if(isset($geoCodes[$v['code']])) selected="selected" @endif >{{$v['name']}}</option>
            @endforeach
        @endif
    @endforeach
</select>
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
        parent.layui.jquery("#menu_ids").val(ids);//给父页面赋值
    });
</script>
