<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $attributes = [
        'published_at' => null,
    ];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
