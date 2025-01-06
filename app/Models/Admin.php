<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'office',
        'email_address',
        'position_id',
        'phone_number',
        'profile_picture',
        'is_deleted',
        'is_super',
        'is_verified',
    ];

    public function getFullnameAttribute() {
        return $this->first_name . ' ' . ($this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '. ' : '') . $this->last_name . ' ' . $this->suffix;
    }

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    use HasFactory;
}
