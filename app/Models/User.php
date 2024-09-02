<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'department_id',
        'personal_bio_id',
        'educational_bio_id',
        'professional_bio_id',
        'is_deleted',
    ];

    public static function allAdmin()
    {
        return User::query()->where('role', '=', 'Admin')->get();
    }

    public function personalBio() {
        return $this->belongsTo(PersonalBio::class, 'personal_bio_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public static function noBio(string $type): Builder
    {
        switch ($type) {
            case 'personal':
                return User::query()->where('personal_bio_id', '=', null);
                break;
            case 'professional':
                return User::query()->where('professional_bio_id', '=', null);
                break;
            case 'educational':
                return User::query()->where('educational_bio_id', '=', null);
                break;
            default:
                throw new Exception('Invalid type: must be [personal, professional, educational]');
        }
    }

    public static function hasBio($type): Builder
    {
        switch ($type) {
            case 'personal':
                return User::query()->where('personal_bio_id', '!=', null);
                break;
            case 'professional':
                return User::query()->where('professional_bio_id', '!=', null);
                break;
            case 'educational':
                return User::query()->where('educational_bio_id', '!=', null);
                break;
            default:
                throw new Exception('Invalid type: must be [personal, professional, educational]');
        }
    }

    public static function isCourse(int $course): Builder 
    {
        $users = self::hasBio('educational');
        $result = array();

        foreach ($users->get() as $user) {
            $bio = $user->getEducationalBio();

            if ($bio->course_id === $course) {
                array_push($result, $user->id);
            }
        }

        return self::query()->whereIn('id', $result);
    }

    public function employment()
    {
        return 'LMAO';
    }

    public function image()
    {
        if ($this->personal_bio_id === null) {
            return fake()->imageUrl();
        }

        $bio = $this->getPersonalBio();
        if (str_contains($bio->profile_picture, 'https://')) {
            return $bio->profile_picture;
        } else {
            return asset('storage/user/images/' . $bio->profile_picture);
        }
    }

    public function getPersonalBio(): null | PersonalBio
    {
        return PersonalBio::query()->find($this->personal_bio_id);
    }

    public function getEducationalBio(): null | EducationalBio
    {
        return EducationalBio::query()->find($this->educational_bio_id);
    }

    public function getProfessionalBio(): null | ProfessionalBio
    {
        return ProfessionalBio::query()->find($this->professional_bio_id);
    }

    public function admin(): null | Admin
    {
        if ($this->role === 'Admin') {
            return Admin::query()->where('user_id', '=', $this->id)->first();
        } else {
            return null;
        }
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
