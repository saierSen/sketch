<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Auth;

trait RewardTrait
{
	public function rewards(){
		return $this->morphMany('App\Models\Reward','rewardable');
	}
}