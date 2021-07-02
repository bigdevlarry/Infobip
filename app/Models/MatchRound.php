<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchRound extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function match()
    {
        return $this->belongsTo(Match::class);
    }

}
