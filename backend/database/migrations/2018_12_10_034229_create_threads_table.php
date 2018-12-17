<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();//作者id
            $table->integer('thread_group');//讨论帖的对应权限：0-水区 1:普通文章 2:仲裁 3:边缘类 10:作业 >10 系统专用
            $table->unsignedInteger('channel_id')->default(0);//是哪个频道下面的
            $table->unsignedInteger('label_id')->default(0);//是哪个大类下面的
            $table->string('title')->nullable();//讨论帖总标题
            $table->string('brief')->nullable();//讨论帖简介小灰字
            $table->text('body')->nullable();//讨论帖正文
            $table->unsignedInteger('last_post_id')->default(0);//最后回帖是谁
            $table->string('last_post_preview')->nullable();//最后回帖的一句话正文
            $table->boolean('is_anonymous')->default(false);//是否匿名
            $table->string('majia', 10)->nullable();//作者马甲
            $table->string('creation_ip', 45)->nullable();//创建时IP地址
            $table->dateTime('created_at')->nullable();//创建时间
            $table->unsignedInteger('last_editor_id')->default(0)->index();//最后是谁编辑的（如果is_anonymous，这一项不应该显示给普通用户）
            $table->dateTime('last_edited_at')->nullable();//最后编辑时间
            $table->boolean('use_markdown')->default(false);//是否使用md语法
            $table->boolean('use_indentation')->default(true);//是否使用段首缩进格式

            $table->integer('xianyus')->default(0);//得到的咸鱼
            $table->integer('shengfans')->default(0);//得到的咸鱼
            $table->unsignedInteger('views')->default(0);//点击数
            $table->integer('replies')->default(0);//得到的回复数
            $table->integer('collections')->default(0);//被收藏次数
            $table->integer('downloads')->default(0);//被下载次数
            $table->integer('jifen')->default(0);//总积分
            $table->integer('weighted_jifen')->default(0);//被字数模块平衡后的积分

            $table->boolean('is_locked')->default(false);//是否锁帖
            $table->boolean('is_public')->default(true);//是否公开
            $table->boolean('is_bianyuan')->default(false);//是否边缘限制
            $table->boolean('no_reply')->default(false);//是否禁止回复
            $table->boolean('is_top')->default(false);//是否置顶
            $table->boolean('is_popular')->default(false);//是否飘火
            $table->boolean('is_highlighted')->default(false);//是否加高亮

            $table->dateTime('last_responded_at')->nullable();//最后被回应时间
            $table->softDeletes();//软删除必备
            $table->integer('book_status')->default(0);//书本进度
            $table->integer('book_length')->default(0);//书本长度
            $table->integer('sexual_orientation')->default(0);//书本性向
            $table->dateTime('last_added_chapter_at')->nullable();//最后被回应时间
            $table->unsignedInteger('last_chapter_id')->default(0);//最新章节
            $table->unsignedInteger('total_char')->default(0);//书籍总字数
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
