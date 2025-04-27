<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'category_id',
        'is_visible',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class)
            ->where('user_id', Filament::auth()->id());
    }

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where('name', 'like', '%'.$searchTerm.'%');
    }

    public function scopeIsVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }
}
