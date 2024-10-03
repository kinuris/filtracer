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

    public static function compSet()
    {
        $compSet = [];
        foreach (User::all() as $user) {
            if ($user->isCompSet()) {
                array_push($compSet, $user->id);
            }
        }

        return User::query()->whereIn('id', $compSet);
    }

    public static function groupBy(string $type)
    {
        switch ($type) {
            case 'Department':
                return User::groupByDepartment();
            case 'Batch':
                return User::groupByBatch();
            case 'Course':
                return User::groupByCourse();
            case 'Jobs':
                return User::groupByJobs();
            default:
                throw new Exception('Invalid type: must be [Department, Batch, Course, Jobs]');
        }
    }

    public static function groupByDepartment()
    {
        $users = User::query()->where('role', '!=', 'Admin')->get();

        $groups = [];
        foreach ($users as $user) {
            if (isset($groups[$user->department->name])) {
                array_push($groups[$user->department->name], $user);
            } else {
                $groups[$user->department->name] = [$user];
            }
        }

        return $groups;
    }

    public static function groupByBatch()
    {
        $users = User::query()->where('role', '!=', 'Admin')->get();

        $groups = [];
        foreach ($users as $user) {
            if (isset($groups[$user->getEducationalBio()->end])) {
                array_push($groups[$user->getEducationalBio()->end], $user);
            } else {
                $groups[$user->getEducationalBio()->end] = [$user];
            }
        }

        krsort($groups);

        return $groups;
    }

    public static function groupByCourse()
    {
        $users = User::query()->where('role', '!=', 'Admin')->get();

        $groups = [];
        foreach ($users as $user) {
            if (isset($groups[$user->course->name])) {
                array_push($groups[$user->course->name], $user);
            } else {
                $groups[$user->course->name] = [$user];
            }
        }

        ksort($groups);

        return $groups;
    }

    public static function groupByJobs()
    {
        $users = User::query()->where('role', '!=', 'Admin')->get();

        $groups = [];
        foreach ($users as $user) {
            $profRec = $user->getProfessionalBio();
            if (isset($groups[$profRec->job_title])) {
                array_push($groups[$profRec->job_title], $user);
            } else {
                $groups[$profRec->job_title] = [$user];
            }
        }

        return $groups;
    }

    public static function countFromGroup($category, $group) {
        switch ($category) {
            case 'Employed Alumni':
                return User::countEmployedFromGroup($group);
            case 'Unemployed Alumni':
                return User::countUnemployedFromGroup($group);
            case 'Self-employed Alumni':
                return User::countSelfEmployedFromGroup($group);
            case 'Working Student':
                return User::countWorkingStudentsFromGroup($group);
            case 'Students':
                return User::countStudentsFromGroup($group);
            case 'Retired':
                return User::countRetiredFromGroup($group);
            case 'All Users':
                return count($group);
            default:
                throw new Exception('INDI PWEDE AHAHAHAHAH Invalid type: must be [Employed, Unemployed, Self-employed, Working Student, Students, Retired, All Users]');
        }
    }

    public static function countRetiredFromGroup($group) {
        $total = 0;
        foreach ($group as $user) {
            if ($user->employment() === 'Retired') {
                $total++;
            }
        }
        
        return $total;
    }

    public static function countWorkingStudentsFromGroup($group) {
        $total = 0;
        foreach ($group as $user) {
            if ($user->employment() === 'Working Student') {
                $total++;
            }
        }
        
        return $total;
    }

    public static function countStudentsFromGroup($group) {
        $total = 0;
        foreach ($group as $user) {
            if ($user->employment() === 'Student') {
                $total++;
            }
        }
        
        return $total;
    }

    public static function countSelfEmployedFromGroup($group) {
        $total = 0;
        foreach ($group as $user) {
            if ($user->employment() === 'Self-employed') {
                $total++;
            }
        }
        
        return $total;
    }

    public static function countUnemployedFromGroup($group) {
        $total = 0;
        foreach ($group as $user) {
            if ($user->employment() === 'Unemployed') {
                $total++;
            }
        }
        
        return $total;
    }

    public static function countEmployedFromGroup($group) {
        $total = 0;
        foreach ($group as $user) {
            if ($user->employment() === 'Employed') {
                $total++;
            }
        }
        
        return $total;
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

    public function alerts()
    {
        return $this->hasMany(UserAlert::class, 'user_id');
    }

    public function getPersonalBio(): null | PersonalRecord
    {
        return PersonalRecord::query()->where('user_id', '=', $this->id)->first();
    }

    public function pinnedPosts()
    {
        return $this->hasMany(PinnedPost::class, 'user_id');
    }

    public function pinnedPostsAsPosts()
    {
        return $this->hasManyThrough(Post::class, PinnedPost::class, 'user_id', 'id', 'id', 'post_id');
    }

    public function isCompSet()
    {
        if ($this->role === 'Admin') {
            return !is_null($this->admin()) && !is_null($this->getPersonalBio());
        }

        return !is_null($this->getEducationalBio()) && !is_null($this->getProfessionalBio()) && !is_null($this->getPersonalBio());
    }

    public function getEducationalBio()
    {
        return EducationRecord::query()
            ->where('user_id', '=', $this->id)
            ->latest()
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
