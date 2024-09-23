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
        'is_deleted',
    ];

    public static function allAdmin()
    {
        return User::query()->where('role', '=', 'Admin')->get();
    }

    public function chatsWith(User $user)
    {
        $groups = array();
        foreach ($this->chatGroups()->get() as $group) {
            if ($group->users()->count() === 2 && $group->users()->get()->contains($user)) {
                array_push($groups, $group);
            }
        }

        return !empty($groups);
    }

    public function chatGroupWith(User $user)
    {
        $groups = array();
        foreach ($this->chatGroups()->get() as $group) {
            if ($group->users()->count() === 2 && $group->users()->get()->contains($user)) {
                array_push($groups, $group);
            }
        }

        return isset($groups[0]) ? $groups[0] : null;
    }

    public function getNameAttribute()
    {
        if ($this->role === 'Alumni') {
            return $this->getPersonalBio()->getFullname();
        } else {
            return $this->attributes['name'];
        }
    }

    public function partialPersonal()
    {
        return $this->hasOne(PartialPersonalRecord::class, 'user_id');
    }

    public function personalBio()
    {
        return $this->hasOne(PersonalRecord::class, 'user_id');
    }

    public function chatGroups()
    {
        return $this->hasManyThrough(ChatGroup::class, ChatAssociation::class, 'user_id', 'id', 'id', 'chat_group_id');
    }

    public function professionalBio()
    {
        return $this->hasOne(ProfessionalRecord::class, 'user_id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, EducationRecord::class, 'user_id', 'id', 'id', 'course_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public static function noBio(string $type): Builder
    {
        switch ($type) {
            case 'personal':
                $ids = PersonalRecord::query()->get('user_id');

                return User::query()->whereNotIn('id', $ids);
                break;
            case 'professional':
                $ids = ProfessionalRecord::query()->get('user_id');

                return User::query()->whereNotIn('id', $ids);
                break;
            case 'educational':
                $ids = EducationRecord::query()->get('user_id');

                return User::query()->whereNotIn('id', $ids);
                break;
            default:
                throw new Exception('Invalid type: must be [personal, professional, educational]');
        }
    }

    public static function hasBio($type): Builder
    {
        switch ($type) {
            case 'personal':
                $ids = PersonalRecord::query()->get('user_id');

                return User::query()->whereIn('id', $ids);
                break;
            case 'professional':
                $ids = ProfessionalRecord::query()->get('user_id');

                return User::query()->whereIn('id', $ids);
                break;
            case 'educational':
                $ids = EducationRecord::query()->get('user_id');

                return User::query()->whereIn('id', $ids);
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
        return $this->getProfessionalBio()->employment_status;
    }

    public function image()
    {
        $bio = $this->getPersonalBio();

        if (is_null($bio)) {
            return fake()->imageUrl();
        }

        if (str_contains($bio->profile_picture, 'https://')) {
            return $bio->profile_picture;
        } else {
            return asset('storage/user/images/' . $bio->profile_picture);
        }
    }

    public function getPersonalBio(): null | PersonalRecord
    {
        return PersonalRecord::query()->where('user_id', '=', $this->id)->first();
    }

    public function getEducationalBio()
    {
        return EducationRecord::query()
            ->where('user_id', '=', $this->id)
            ->orderBy('end', 'DESC')
            ->first();
    }

    public function educationalBios()
    {
        return $this->hasMany(EducationRecord::class, 'user_id')->orderBy('end', 'DESC');
    }

    public function getProfessionalBio(): null | ProfessionalRecord
    {
        return ProfessionalRecord::query()->where('user_id', '=', $this->id)->first();
    }

    public function professionalRecords()
    {
        return $this->hasOne(ProfessionalRecord::class, 'user_id');
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
