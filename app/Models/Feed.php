<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
	protected $guarded = [];

	protected $casts = [
		'active' => 'boolean'
	];

	public function posts() {
		return $this->hasMany(Post::class);
	}

	public function categories() {
        return $this->belongsToMany(Category::class);
    }

	public function categoryIds() {
		return $this->categories->pluck('category_id');
	}
}
