<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'logo',
    ];

    public function getCourses() {
        return Course::query()->where('department_id', '=', $this->id)->get();
    }

    public function students() {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    public static function allValid() {
        return Department::query()->where('name', '!=', 'Admins Assigned')->get();
    } 

    use HasFactory;
}
