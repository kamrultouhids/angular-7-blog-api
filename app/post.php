<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    protected $fillable = [
        'category_id','title','description','date','created_by','post_slug','status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
