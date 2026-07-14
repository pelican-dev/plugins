<?php

namespace Boy132\Subdomains\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class NotOnBlacklist implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('validation.string')->translate();
        }

        $value = (string) $value;
        $blacklist = array_filter(explode(',', config('subdomains.subdomain_blacklist')));

        foreach ($blacklist as $check) {
            if (Str::is($check, $value, true)) {
                $fail('subdomains::strings.validation.on_blacklist')->translate();
            }
        }
    }
}
