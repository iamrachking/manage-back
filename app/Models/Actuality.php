<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Actuality extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'description', 'cover_path', 'category_id', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo 
    {
        return $this->belongsTo(Category::class);
    }
    
    public function galleries(): MorphMany
    {
        return $this->morphMany(Gallery::class, 'galleriestable');
    }
}