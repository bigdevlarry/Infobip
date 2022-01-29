<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tournament extends Model
{
    use SoftDeletes; use HasFactory;
    
    protected $guarded = ['id'];

}
