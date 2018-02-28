@extends('layouts.default')
@section('title', $book->thread->title.'-更新章节')
@section('content')
   <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
     <div class="panel panel-default">
       <div class="panel-heading">
          <a type="btn btn-primary" href="{{ route('home') }}"><span class="glyphicon glyphicon-home"></span><span>首页</span></a>/<a href="{{ route('channel.show', $book->thread->channel_id) }}">{{ $book->thread->channel->channelname }}</a>/<a href="{{ route('label.show', $book->thread->label) }}">{{ $book->thread->label->labelname }}</a>/{{ $book->thread->title }}
       </div>
       <div class="panel-body">
         @include('shared.errors')
         <form method="POST" action="{{ route('book.storechapter', $book) }}" name="create_book_chapter">
           {{ csrf_field() }}
             <div class="form-group">
               <label for="title"><h4>章节名称：</h4></label>
               <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="章节名称">
             </div>
             <div class="form-group">
               <label for="brief"><h4>概要：</h4></label>
               <input type="text" name="brief" class="form-control" value="{{ old('brief') }}">
             </div>
             <div class="form-group">
               <label for="body"><h4>正文：</h4></label>
               <textarea id="mainbody" name="body" rows="12" class="form-control" data-provide="markdown" placeholder="章节正文">{{ old('body') }}</textarea>
               <button type="button" onclick="retrievecache('mainbody')" class="sosad-button-control addon-button">恢复数据</button>
               <button href="#" type="button" onclick="wordscount('mainbody');return false;" class="pull-right sosad-button-control addon-button">字数统计</button>
               <label><input type="checkbox" name="markdown">使用Markdown语法？</label>
               <label><input type="checkbox" name="indentation" checked>段首缩进？</label>
             </div>
             <div class="form-group">
               <label for="annotation"><h4>备注：</h4></label>
               <textarea id="mainannotation" name="annotation" data-provide="markdown" rows="5" class="form-control" placeholder="作者有话说…">{{ old('annotation') }}</textarea>
               <button type="button" onclick="retrievecache('mainannotation')" class="sosad-button-control addon-button">恢复数据</button>
               <button href="#" type="button" onclick="wordscount('mainannotation');return false;" class="pull-right sosad-button-control addon-button">字数统计</button>
             </div>
             @if(!$book->thread->anonymous)
             <div class="checkbox">
               <label><input type="checkbox" name="sendstatus" checked>更新动态？</label>
             </div>
             @endif
             <button type="submit" class="btn btn-primary sosad-button">发布新章节</button>
         </form>
       </div>
     </div>
   </div>
@stop
