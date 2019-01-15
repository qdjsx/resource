@extends('list')
@section('body_content')
<div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{$item->name}}-详情 </h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form method="get" class="form-horizontal">
                                <div class="form-group">
                                	<label class="col-lg-2 control-label">名称</label>

                                    <div class="col-lg-10">
                                    	<p class="form-control-static">{{$item->name}}</p>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                	<label class="col-lg-2 control-label">邮箱</label>

                                    <div class="col-lg-10">
                                    	<p class="form-control-static">{{$item->email}}</p>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                	<label class="col-lg-2 control-label">创建时间</label>

                                    <div class="col-lg-10">
                                    	<p class="form-control-static">{{$item->created_at}}</p>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                	<label class="col-lg-2 control-label">更新时间</label>

                                    <div class="col-lg-10">
                                    	<p class="form-control-static">{{$item->updated_at}}</p>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
@endsection