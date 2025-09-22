<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'logo', 'company_email', 'container_type', 'company_phone', 'company_address', 'mail_signature', 'date_format', 'timezone',  'country', 'copyright_text'];

    protected static function active()
    {
        return self::withoutTrashed();
    }

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? asset('storage/uploads/' . $value) : $value,
        );
    }
}
