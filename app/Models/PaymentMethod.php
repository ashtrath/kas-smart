<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $icon
 * @property array<array-key, mixed> $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|PaymentMethod newModelQuery()
 * @method static Builder<static>|PaymentMethod newQuery()
 * @method static Builder<static>|PaymentMethod query()
 * @method static Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static Builder<static>|PaymentMethod whereDescription($value)
 * @method static Builder<static>|PaymentMethod whereDetails($value)
 * @method static Builder<static>|PaymentMethod whereIcon($value)
 * @method static Builder<static>|PaymentMethod whereId($value)
 * @method static Builder<static>|PaymentMethod whereName($value)
 * @method static Builder<static>|PaymentMethod whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PaymentMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'details',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }
}
