<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

	protected $casts = [
		'content' => 'array'
    ];

	public function feed() {
		return $this->belongsTo(Feed::class);
	}

	public function categories() {
		return $this->belongsToMany(Category::class, 'category_feed', 'feed_id', null, 'feed_id');
	}

	public function scopeFilter($query, $filters = []) {
		if(!$filters) return $query;

		if (isset($filters['active'])) {
			$query->whereHas('feed', function ($q) use ($filters) {
				$q->where('active', $filters['active']);
			});
		}

		$query->when($filters['feeds'] ?? null, function ($query, $feeds) {
			$query->whereIn('feed_id', $feeds);
		})->when($filters['type'] ?? null, function ($query, $type) {
			$query->where('type', $type);
		})->when($filters['categories'] ?? null, function ($query, $categories) {
			$query->whereHas('categories', function ($q) use ($categories) {
				$q->whereIn('id', $categories);
			});
		});
	}
}
