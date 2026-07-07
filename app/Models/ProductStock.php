<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'branch_id',
        'qty',
    ];

    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
        'branch_id'  => 'integer',
        'qty'        => 'integer',
    ];

    /**
     * Get the product that owns this stock record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the branch that owns this stock record.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
