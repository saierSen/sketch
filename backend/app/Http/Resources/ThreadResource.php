<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;
use App\Helpers\Helper;

class ThreadResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        return [
            'type' => 'thread',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'brief' => $this->brief,
                'last_post_id' => $this->last_post_id,
                'last_post_preview' => $this->last_post_preview,
                'is_anonymous' => $this->is_anonymous,
                'majia' => $this->majia ?? '匿名咸鱼',
                'created_at' => $this->created_at ? $this->created_at->toDateTimeString():null,
                'xianyus' => $this->xianyus,
                'shengfans' => $this->shengfans,
                'views' => $this->views,
                'replies' => $this->replies,
                'collections' => $this->collections,
                'downloads' => $this->downloads,
                'jifen' => $this->jifen,
                'weighted_jifen' => $this->weighted_jifen,
                'is_locked' => $this->is_locked,
                'is_public' => $this->is_public,
                'is_bianyuan' => $this->is_bianyuan,
                'no_reply' => $this->no_reply,
                'is_top' => $this->is_top,
                'is_popular' => $this->is_popular,
                'is_highlighted' => $this->is_highlighted,
                'last_responded_at' => $this->last_responded_at? $this->last_responded_at->toDateTimeString():null,
                'book_status' =>  config('constants.book_info.book_status_info')[$this->book_status],
                'book_length' => config('constants.book_info.book_length_info')[$this->book_length],
                'sexual_orientation' => config('constants.book_info.sexual_orientation_info')[$this->sexual_orientation],
                'last_added_chapter_at' =>$this->last_added_chapter_at? $this->last_added_chapter_at->toDateTimeString():null,
                'last_chapter_id' => $this->last_chapter_id,
                'total_char' => $this->total_char,
            ],
            'relationships' => new ThreadRelationshipResource($this),
        ];
    }

}
