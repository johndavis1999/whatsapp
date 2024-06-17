<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Contact;
use App\Models\User;

class InvalidEmail implements ValidationRule
{
    public $email;

    public function __construct($email = null){
        $this->email = $email;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $addContact = Contact::where('user_id', auth()->id())
            ->whereHas('user', function($query) use ($value) {
                $query->where('email', $value)
                        ->when($this->email, function($query){
                            $query->where('email', '!=', $this->email);
                        });
            })->count() == 0;

        if (!$addContact) {
            $fail('El usuario ya se encuentra registrado.');
        }
    }
}
