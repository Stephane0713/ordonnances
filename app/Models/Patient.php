<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'ssn',
        'contact_method',
        'contact_value',
        'consent_file',
    ];

    public function getSSN()
    {
        $ssn = (string) $this->ssn;
        return str_pad($ssn, 13, '*', STR_PAD_RIGHT);
    }

    public function setSsnAttribute($value)
    {
        $digits = preg_replace('/\D/', '', $value);
        $this->attributes['ssn'] = substr($digits, 0, 8);
    }
}
