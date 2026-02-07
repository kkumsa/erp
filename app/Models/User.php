<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'is_active',
        'locale',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'preferences' => 'array',
        ];
    }

    /**
     * 사용자 환경설정 값 조회
     */
    public function getPreference(string $key, mixed $default = null): mixed
    {
        return data_get($this->preferences, $key, $default);
    }

    /**
     * 사용자 환경설정 값 저장
     */
    public function setPreference(string $key, mixed $value): void
    {
        $preferences = $this->preferences ?? [];
        data_set($preferences, $key, $value);
        $this->preferences = $preferences;
        $this->save();
    }

    /**
     * 알림 수신 여부 확인
     * 기본값은 true (설정하지 않은 알림은 수신)
     */
    public function wantsNotification(string $notificationType): bool
    {
        return (bool) $this->getPreference("notifications.{$notificationType}", true);
    }

    /**
     * 알림 설정 일괄 조회
     */
    public function getNotificationPreferences(): array
    {
        return $this->getPreference('notifications', []);
    }

    /**
     * 알림 설정 일괄 저장
     */
    public function setNotificationPreferences(array $settings): void
    {
        $this->setPreference('notifications', $settings);
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active', 'avatar_url', 'password'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * 활동 로그에서 비밀번호 해시값 마스킹
     */
    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName): void
    {
        $properties = $activity->properties->toArray();

        // password 필드를 '(변경됨)' 으로 대체
        if (isset($properties['attributes']['password'])) {
            $properties['attributes']['password'] = '(변경됨)';
        }
        if (isset($properties['old']['password'])) {
            $properties['old']['password'] = '(이전 비밀번호)';
        }

        // avatar_url 필드를 간소화
        if (isset($properties['attributes']['avatar_url'])) {
            $properties['attributes']['avatar_url'] = $properties['attributes']['avatar_url'] ? '(새 사진)' : '(삭제됨)';
        }
        if (isset($properties['old']['avatar_url'])) {
            $properties['old']['avatar_url'] = $properties['old']['avatar_url'] ? '(이전 사진)' : '(없음)';
        }

        $activity->properties = collect($properties);
    }

    /**
     * Filament 접근 권한 확인
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    /**
     * Filament 아바타
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }

        return $this->getInitialsAvatarUrl();
    }

    /**
     * 이름에서 이니셜 추출
     * - 한 단어: 첫 글자
     * - 두 단어 이상: 각 단어의 첫 글자
     */
    public function getInitials(): string
    {
        $name = trim($this->name ?? '?');
        $words = preg_split('/\s+/', $name);

        if (count($words) >= 2) {
            // 두 단어 이상: 각 단어의 첫 글자
            $initials = '';
            foreach ($words as $word) {
                $initials .= mb_substr($word, 0, 1);
            }
            return mb_strtoupper(mb_substr($initials, 0, 2));
        }

        // 한 단어: 첫 글자
        return mb_strtoupper(mb_substr($name, 0, 1));
    }

    /**
     * 이름 기반 고정 색상 (해시로 일관된 색상)
     */
    public function getAvatarColor(): string
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#F97316', '#6366F1', '#14B8A6',
            '#E11D48', '#7C3AED', '#0EA5E9', '#D946EF', '#84CC16',
        ];

        $hash = crc32($this->name ?? 'unknown');
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * SVG 이니셜 아바타를 Data URI로 생성
     */
    public function getInitialsAvatarUrl(): string
    {
        $initials = $this->getInitials();
        $color = $this->getAvatarColor();
        $fontSize = mb_strlen($initials) > 1 ? '38' : '44';

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">'
            . '<rect width="100" height="100" rx="50" fill="' . $color . '"/>'
            . '<text x="50" y="50" text-anchor="middle" dominant-baseline="central" '
            . 'fill="white" font-family="sans-serif" font-weight="600" font-size="' . $fontSize . '">'
            . htmlspecialchars($initials)
            . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * notifications 관계 오버라이드 (SoftDeletes 적용)
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    /**
     * 사용자의 직원 정보
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * 로그인 이력
     */
    public function loginHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoginHistory::class)->latest('created_at');
    }
}
