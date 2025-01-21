<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $product_id
 * @property int $quantity
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TransactionItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
