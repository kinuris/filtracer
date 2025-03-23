<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BindingRequest extends Model
{
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function toBoundAccount() {
        $bound = new BoundAccount();

        $bound->alumni_id = $this->alumni_id;
        $bound->admin_id= $this->admin_id;

        $bound->save();
        $this->delete();

        return $bound;
    }
}
