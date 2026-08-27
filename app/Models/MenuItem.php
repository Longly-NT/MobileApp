<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'allergy_info',
        'price',
        'image',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('supabase')->url($this->image);
    }
}
