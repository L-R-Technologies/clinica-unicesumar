<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $machine_id
 * @property string $name
 * @property string $token_hash
 * @property string $token_prefix
 * @property string|null $agent_version
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property bool $active
 * @property-read Machine|null $machine
 */
class LabAgent extends Model
{
    use HasFactory, LogsActivity;

    public const TOKEN_LENGTH = 48;

    public const TOKEN_PREFIX_LENGTH = 8;

    protected $fillable = [
        'machine_id',
        'name',
        'token_hash',
        'token_prefix',
        'agent_version',
        'last_seen_at',
        'active',
    ];

    protected $hidden = ['token_hash'];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function captures(): HasMany
    {
        return $this->hasMany(LabCapture::class);
    }

    public static function generatePlainToken(): string
    {
        return Str::random(self::TOKEN_LENGTH);
    }

    public static function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    public static function findByPlainToken(string $plainToken): ?self
    {
        return self::where('token_hash', self::hashToken($plainToken))->first();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['machine_id', 'name', 'token_prefix', 'active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('lab_agent');
    }
}
