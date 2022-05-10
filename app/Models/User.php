<?php

namespace App\Models;

use App\Models\Scopes\PeriodScope;
use App\Models\Scopes\UserRoleScope;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use StandardDateFormat;
    use PeriodScope;
    use UserRoleScope;

    const ROLE_USER  = 1;
    const ROLE_ADMIN = 2;
    const ROLE_BOT   = 4;

    const STATUS_ACTIVE  = 0;
    const STATUS_BLOCKED = 1;

    // General permissions
    const PERMISSION_ACCOUNTS = 'accounts';
    const PERMISSION_ADDONS = 'add-ons';
    const PERMISSION_AFFILIATE = 'affiliate';
    const PERMISSION_CHAT = 'chat';
    const PERMISSION_DASHBOARD = 'dashboard';
    const PERMISSION_GAMES = 'games';
    const PERMISSION_LICENSE = 'license';
    const PERMISSION_HELP = 'help';
    const PERMISSION_MAINTENANCE = 'maintenance';
    const PERMISSION_SETTINGS = 'settings';
    const PERMISSION_USERS = 'users';
    // Payments
    const PERMISSION_DEPOSITS = 'deposits';
    const PERMISSION_DEPOSIT_METHODS = 'deposit-methods';
    const PERMISSION_WITHDRAWALS = 'withdrawals';
    const PERMISSION_WITHDRAWAL_METHODS = 'withdrawal-methods';
    // Raffle
    const PERMISSION_RAFFLES = 'raffles';

    const ACCESS_NONE = 0;
    const ACCESS_READONLY = 1;
    const ACCESS_FULL = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'status', 'social_id', 'role', 'code', 'password', 'avatar', 'hide_profit', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'code', 'password', 'remember_token', 'totp_secret', 'referrer_id', 'referrer'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'bet_count'         => 'integer',
        'bet_total'         => 'float',
        'profit_total'      => 'float',
        'profit_max'        => 'float',
        'hide_profit'       => 'boolean',
        'banned_from_chat'  => 'boolean',
        'permissions'       => 'collection',
        'role'              => 'integer',
        'status'            => 'integer',
    ];

    /**
     * Auto-cast the following columns to Carbon
     *
     * @var array
     */
    protected $dates = [
        'last_login_at', 'email_verified_at', 'last_seen_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url', 'gravatar_url', 'created_ago', 'last_seen_ago', 'role_title', 'status_title'
    ];

    /**
     * User, who referred current user (referrer)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrer()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Users, referred by current user (referees)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referees()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('users.status', '=', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include bots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBots($query): Builder
    {
        return $query->where('users.role', '=', self::ROLE_BOT);
    }

    /**
     * Scope a query to only include regular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegular($query)
    {
        return $query->where('users.role', '=', self::ROLE_USER);
    }

    /**
     * Scope a query to only include regular non-admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotAdmin($query)
    {
        return $query->where('users.role', '!=', self::ROLE_ADMIN);
    }

    /**
     * User account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne(Account::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(AccountTransaction::class, Account::class);
    }

    public function commission()
    {
        return $this->morphMany(AffiliateCommission::class, 'commissionable');
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, Account::class);
    }

    /**
     * Chat messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Custom gravatar_url attribute
     *
     * @return string
     */
    public function getGravatarUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=100&d=mp';
    }

    /**
     * Mutator for hide_profit
     */
    public function setHideProfitAttribute($value)
    {
        $this->attributes['hide_profit'] = is_bool($value) ? $value : ($value == 1 || $value == 'true');
    }

    /**
     * Custom avatar_url attribute
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/avatars/' . $this->avatar) : $this->gravatar_url;
    }

    /**
     * Custom two_factor_auth_enabled attribute
     * @return bool
     */
    public function getTwoFactorAuthEnabledAttribute()
    {
        return $this->totp_secret ? TRUE : FALSE;
    }

    /**
     * Custom two_factor_auth_passed attribute
     * @return bool
     */
    public function getTwoFactorAuthPassedAttribute()
    {
        return request()->session()->get('two_factor_auth_passed', FALSE);
    }

    /**
     * Custom is_admin attribute
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Custom is_bot attribute
     * @return bool
     */
    public function getIsBotAttribute(): bool
    {
        return $this->hasRole(self::ROLE_BOT);
    }

    /**
     * Custom is_active attribute
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Custom is_online attribute
     * @return bool
     */
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gte(Carbon::now()->subSeconds($this->is_bot ? 300 : 120));
    }

    /**
     * Mutator for is_online attribute
     *
     * @param bool $value
     */
    public function setIsOnlineAttribute(bool $value)
    {
        if ($value) {
            $this->attributes['last_seen_at'] = Carbon::now();
        }
    }

    /**
     * Social profiles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany(SocialProfile::class);
    }

    /**
     * Override original model's delete() method
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->avatar)
            Storage::disk('public')->delete('avatars/' . $this->avatar);

        return parent::delete();
    }

    /**
     * Determine if the user has verified their email address.
     * Override Illuminate\Auth\MustVerifyEmail@hasVerifiedEmail()
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at) || $this->is_bot;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Check if user has given role
     *
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return isset($this->role) && $this->role == $role;
    }

    /**
     * Check if a given user has read-only access to a given module
     *
     * @param string $module
     * @return bool
     */
    public function hasReadOnlyAccess(string $module)
    {
        return is_null($this->permissions) || (int)$this->permissions->get($module) >= self::ACCESS_READONLY;
    }

    /**
     * Check if a given user has full access to a given module
     *
     * @param string $module
     * @return bool
     */
    public function hasFullAccess(string $module)
    {
        return is_null($this->permissions) || (int)$this->permissions->get($module) >= self::ACCESS_FULL;
    }

    // automatically decrypt TOTP secret when it's requested
    public function getTotpSecretAttribute($value)
    {
        return $value ? decrypt($value) : NULL;
    }

    // encrypt TOTP secret in the database
    public function setTotpSecretAttribute($value)
    {
        $this->attributes['totp_secret'] = encrypt($value);
    }

    public static function roles()
    {
        return [
            self::ROLE_USER => __('User'),
            self::ROLE_ADMIN => __('Admin'),
            self::ROLE_BOT => __('Bot'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_BLOCKED => __('Blocked'),
        ];
    }

    public static function accessModes()
    {
        return [
            self::ACCESS_NONE => __('None'),
            self::ACCESS_READONLY => __('Read only'),
            self::ACCESS_FULL => __('Full'),
        ];
    }

    public static function permissions()
    {
        return [
            self::PERMISSION_DASHBOARD => __('Dashboard'),
            self::PERMISSION_USERS => __('Users'),
            self::PERMISSION_ACCOUNTS => __('Accounts'),
            self::PERMISSION_AFFILIATE => __('Affiliate'),
            self::PERMISSION_GAMES => __('Games'),
            self::PERMISSION_CHAT => __('Chat'),
            self::PERMISSION_SETTINGS => __('Settings'),
            self::PERMISSION_MAINTENANCE => __('Maintenance'),
            self::PERMISSION_ADDONS => __('Add-ons'),
            self::PERMISSION_LICENSE => __('License'),
            self::PERMISSION_HELP => __('Help'),
            self::PERMISSION_RAFFLES => __('Raffles'),
            self::PERMISSION_DEPOSITS => __('Deposits'),
            self::PERMISSION_DEPOSIT_METHODS => __('Deposit methods'),
            self::PERMISSION_WITHDRAWALS => __('Withdrawals'),
            self::PERMISSION_WITHDRAWAL_METHODS => __('Withdrawal methods'),
        ];
    }

    /**
     * Custom getter for created_ago attribute
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : NULL;
    }

    /**
     * Custom getter for last_seen_ago attribute
     */
    public function getLastSeenAgoAttribute()
    {
        return $this->last_seen_at ? $this->last_seen_at->diffForHumans() : NULL;
    }

    public function getRoleTitleAttribute()
    {
        return self::roles()[$this->role] ?? '';
    }

    public function getStatusTitleAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }
}
