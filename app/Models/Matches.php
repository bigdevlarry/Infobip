<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Matches extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = "matches";

    protected $guarded = ['id'];


    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

}
