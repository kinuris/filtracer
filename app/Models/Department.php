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

    public function shortened() {
        $result = '';

        foreach (str_split($this->name) as $char) {
            if (ctype_upper($char)) {
                $result .= $char;
            }
        }

        return $result;
    }

    public function getCourses() {
        return Course::query()->where('department_id', '=', $this->id)->get();
    }

    public function students() {
        return $this->hasMany(User::class, 'department_id', 'id')->where('role', '=', 'Alumni');
    }

    public static function allValid() {
        return Department::query()->where('name', '!=', 'Admins Assigned')->get();
    } 

    use HasFactory;
}
