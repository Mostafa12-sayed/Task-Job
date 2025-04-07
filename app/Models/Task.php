<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'user_id',
    ];


    public function getCreatedAtAttribute($key)
    {
        return Carbon::parse($key)->diffForHumans();
    }


}
