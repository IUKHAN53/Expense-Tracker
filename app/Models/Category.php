<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use BelongsToAccount;

    protected $fillable = [
        'account_id',
        'name',
        'icon',
        'color',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }
}
