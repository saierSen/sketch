<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreBook;
use App\Models\Thread;
use App\Helpers\ConstantObjects;

use Auth;

class BooksController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only('create', 'store', 'update','edit');
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(StoreBook $form)
    {
        $thread = $form->generateBook();
        $thread->user->reward("regular_book");
        return redirect()->route('book.show', $thread->book_id)->with("success", "您已成功发布文章");
    }
    public function edit(Book $book)
    {
        if ((Auth::id() == $book->thread->user_id)&&(!$book->thread->locked)){
            $thread = $book->thread->load('mainpost');
            $book->load('tongren');
            $tags = $thread->tags->pluck('id')->toArray();
            return view('books.edit',compact('book', 'thread','tags'));
        }else{
            return redirect()->route('error', ['error_code' => '405']);
        }
    }
    public function update(StoreBook $form, Book $book)
    {
        $thread = $book->thread;
        if ((Auth::id() == $book->thread->user_id)&&(!$thread->locked)){
            $form->updateBook($thread);
            return redirect()->route('book.show', $book->id)->with("success", "您已成功修改文章");
        }else{
            return redirect()->route('error', ['error_code' => '405']);
        }
    }

    public function show($id, Request $request)
    {   $book = DB::table('books')->where('id','=',$id)->first();
        redirect()->route('thread.show_profile', $book->thread_id);
    }



    public function index(Request $request)
    {
        $tags = ConstantObjects::organizeBookTags();

        $queryid = 'bookQ'
        .url('/')
        .'-inChannel'.$request->inChannel
        .'-withBianyuan'.$request->withBianyuan
        .'-withTag'.$request->withTag
        .'-excludeTag'.$request->excludeTag
        .'-ordered'.$request->ordered
        .(is_numeric($request->page)? 'P'.$request->page:'P1');
        $threads = Cache::remember($queryid, 5, function () use($request) {
            return Thread::with('author', 'tags', 'last_component', 'last_post')
            ->inChannel($request->inChannel)
            ->isPublic()
            ->withType('book')
            ->withBianyuan($request->withBianyuan)
            ->withTag($request->withTag)
            ->excludeTag($request->excludeTag)
            ->ordered($request->ordered)
            ->paginate(config('preference.threads_per_page'))
            ->appends($request->only('inChannel','withBianyuan','withTag','excludeTag','ordered'));
        });

        return view('books.index', compact('threads','tags'));
    }

    public function selector($bookquery_original, Request $request)
    {
        $book_info = config('constants.book_info');
        $bookquery=explode('-',$bookquery_original);
        $bookinfo=[];
        foreach($bookquery as $info){
            array_push($bookinfo,array_map('intval',explode('_',$info)));
        }
        $logged = Auth::check()? true:false;
        $page = is_numeric($request->page) ? $request->page:1;
        $bookselectorid = 'booksSelector'
        .url('/')
        .($logged? '-Loggedd':'-notLogged')//logged or not
        .$bookquery_original
        .(is_numeric($request->page)? 'P'.$request->page:'P1');
        $books = Cache::remember($bookselectorid, 10, function () use($bookinfo, $page, $logged, $book_info, $request) {
            if((!empty($bookinfo[5]))&&($bookinfo[5][0]>0)){//用户是否提交了标签(tag)筛选要求？
                $query = $this->join_complex_book_tables();//包含标签筛选
            }else{
                $query = $this->join_book_tables();//不包含标签筛选
            }
            $query->where([['threads.deleted_at', '=', null],['threads.public','=',1]]);
            if(!$logged){$query = $query->where('bianyuan','=',0);}//未登陆用户不能进一步看限制文
            if(!empty($bookinfo[0])&&count($bookinfo[0])==1){//原创性筛选
                $query->where('threads.channel_id','=', $bookinfo[0][0]);
            }
            if((!empty($bookinfo[1]))&&count($bookinfo[1])<count($book_info['book_length_info'])){//书籍长度筛选
                $query->whereIn('books.book_length',$bookinfo[1]);
            }
            if((!empty($bookinfo[2]))&&count($bookinfo[2])<count($book_info['book_status_info'])){//书籍进度筛选
                $query->whereIn('books.book_status',$bookinfo[2]);
            }
            if((!empty($bookinfo[3]))&&count($bookinfo[3])<count($book_info['sexual_orientation_info'])){//书籍性向筛选
                $query->whereIn('books.sexual_orientation',$bookinfo[3]);
            }
            if((!empty($bookinfo[4]))&&count($bookinfo[4])<count($book_info['rating_info'])){//书籍限制性筛选
                $query->where('threads.bianyuan','=',$bookinfo[4][0]-1);
            }
            if((!empty($bookinfo[5]))&&($bookinfo[5][0]>0)){//标签筛选
                $query->whereIn('tagging_threads.tag_id',$bookinfo[5]);
            }
            if(!empty($bookinfo[6])){//排序方式筛选
                $query = $this->bookOrderBy($query, $bookinfo[6][0]);
            }
            $books = $this->return_book_fields($query)
            ->distinct()
            ->paginate(config('constants.index_per_page'))
            ->appends($request->only('page'));
            return $books;
        });
        return view('books.index', compact('books'))->with('show_as_collections', false)->with('show_bianyuan_tab', false);
    }

    public function booktag(Tag $booktag, Request $request){
        // $logged = Auth::check()? true:false;
        // $tagqueryid = 'tagQuery'
        // .url('/')
        // .($logged? '-Loggedd':'-notLogged')//logged or not
        // .($request->showbianyuan? '-ShowBianyuan':'')
        // .$booktag->id
        // .($request->orderby? '-Orderby'.$request->orderby:'-defaultOrderBy')
        // .(is_numeric($request->page)? 'P'.$request->page:'P1');
        // $books = Cache::remember($tagqueryid, 2, function () use($request, $booktag, $logged) {
        //     $query = $this->join_book_tables();
        //     $query = $this->filter_tag($query, $booktag);
        //     $query->where([['threads.deleted_at', '=', null],['threads.public','=',1]]);
        //     if((!$logged)||(!$request->showbianyuan)){$query = $query->where('bianyuan','=',0);}
        //     $query = $this->return_book_fields($query);
        //     $books = $this->bookOrderBy($query, $request->orderby)
        //     ->paginate(config('constants.index_per_page'))
        //     ->appends($request->only('page','showbianyuan'));
        //     return $books;
        // });
        // return view('books.index', compact('books'))->with('show_as_collections', false)->with('show_bianyuan_tab', true);
    }
    public function filter_tag($query, $tag_id)
    {
        $tag = Cache::remember('book-tag'.$tag_id, 30, function () use($tag_id) {
            return Tag::find($tag_id);
        });
        if($tag->tag_group===10){
            return $query->where('tongren_yuanzhu_tags.id','=',$tag->id);
        }
        if($tag->tag_group===20){
            return $query->where('tongren_cp_tags.id','=',$tag->id);
        }
        return $query->join('tagging_threads','threads.id','=','tagging_threads.thread_id')
        ->where('tagging_threads.tag_id','=',$tag->id);
    }

    public function filter(Request $request){
        $bookquery='';
        //[0]原创性
        if(request('original')){
            foreach(request('original') as $i=>$query){
                if($i>0){
                    $bookquery.='_';
                }
                $bookquery.=$query;
            }
        }else{
            $bookquery.=0;
        }
        //[1]篇幅
        $bookquery.='-';
        if(request('length')){
            foreach(request('length') as $i=>$query){
                if($i>0){
                    $bookquery.='_';
                }
                $bookquery.=$query;
            }
        }else{
            $bookquery.=0;
        }
        //[2]进度
        $bookquery.='-';
        if(request('status')){
            foreach(request('status') as $i=>$query){
                if($i>0){
                    $bookquery.='_';
                }
                $bookquery.=$query;
            }
        }else{
            $bookquery.=0;
        }
        //[3]性向
        $bookquery.='-';
        if(request('sexual_orientation')){
            foreach(request('sexual_orientation') as $i=>$query){
                if($i>0){
                    $bookquery.='_';
                }
                $bookquery.=$query;
            }
        }else{
            $bookquery.=0;
        }
        //[4]边缘限制性
        $bookquery.='-';
        if(request('rating')){
            foreach(request('rating') as $i=>$query){
                if($i>0){
                    $bookquery.='_';
                }
                $bookquery.=$query;
            }
        }else{
            $bookquery.=0;
        }
        //[5]标签
        $bookquery.='-';
        if(request('tag')){
            foreach(request('tag') as $i=>$query){
                if($i>0){
                    $bookquery.='_';
                }
                $bookquery.=$query;
            }
        }else{
            $bookquery.=0;
        }
        //[6]排序方式
        $bookquery.='-';
        if(request('orderby')){
            $bookquery.=request('orderby');
        }else{
            $bookquery.=1;
        }
        return redirect()->route('books.selector',$bookquery);
    }

    public function tags()
    {
        return view('books.tags');
    }

    public function bookselector()
    {
        return view('books.selector');
    }
}
