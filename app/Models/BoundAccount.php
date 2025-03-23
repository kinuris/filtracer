<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoundAccount extends Model
{
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function alumni() {
        return $this->belongsTo(User::class, 'alumni_id');
    }
}
