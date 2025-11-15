<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author_id', 'category_id', 'publisher_id', 'published_year', 'cover_path'];

    public function getCoverUrlAttribute()
{
    if ($this->cover_path) {
        return \Illuminate\Support\Facades\Storage::url($this->cover_path);
    }
    
    return null;
}

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'borrowings')
                ->withPivot('id','borrowed_at', 'returned_at')
                ->withTimestamps();
}


}