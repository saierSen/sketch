@extends('layouts.default')
@section('title', '出错啦')

@section('content')
<div class="container-fluid">
    <div class="content">
        <div class="title">
            <h1>数据异常</h1>
            <h4>输入的参数和数据库中原有数据发生了矛盾，无法完成相关操作/储存相关数据</h4>
            <h4>解决办法：请等待缓存时间过后返回个人中心，仔细核实是否已经建立了文章/讨论帖，仔细检查输入数据，确认无误后重新从正确的入口进入页面提交数据。</h4>
            <h6>详情/参数：{{ $exception->getMessage() }}</h6>
        </div>
    </div>
</div>
@stop