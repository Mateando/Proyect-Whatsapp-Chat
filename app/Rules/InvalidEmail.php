<?php

namespace App\Rules;

use App\Models\Contact;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InvalidEmail implements ValidationRule
{
    
    public $email;

    public function __construct($email = null)
    {
        $this->email = $email;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Contact::where('user_id', auth()->id())
            ->whereHas('user', function ($query) use ($value) {
                $query->where('email', $value)
                    ->when($this->email, function ($query) {
                        $query->where('email', '!=', $this->email);
                    });                    
            })->exists() ? $fail('El correo ya fue añadido con anterioridad.') : null;
    }
}
