<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule that "exists" but only against rows visible to the current
 * tenant. Wraps a scoped Eloquent model so the AccountScope global filter
 * runs — preventing one user from referencing another user's IDs in
 * spending_list_id / category_id fields.
 *
 *     'spending_list_id' => ['required', 'integer', new OwnedByTenant(SpendingList::class)],
 */
class OwnedByTenant implements ValidationRule
{
    public function __construct(private string $modelClass) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $exists = $this->modelClass::query()->whereKey($value)->exists();

        if (! $exists) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
