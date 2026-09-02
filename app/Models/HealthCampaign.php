<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $date_from
 * @property \Illuminate\Support\Carbon|null $date_to
 * @property array<int, int>|null $exam_type_ids
 * @property int $patients_count
 * @property int $exams_count
 * @property string $dataset
 * @property array<string, mixed>|null $analysis
 * @property array<string, mixed>|null $campaign
 * @property string|null $model
 * @property string $status
 * @property string|null $error_message
 * @property-read User|null $user
 */
class HealthCampaign extends Model
{
    use LogsActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'date_from',
        'date_to',
        'exam_type_ids',
        'patients_count',
        'exams_count',
        'dataset',
        'analysis',
        'campaign',
        'model',
        'status',
        'error_message',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'exam_type_ids' => 'array',
        'analysis' => 'array',
        'campaign' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_FAILED], true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        // Análise, campanha e dataset são grandes: o log guarda só os metadados.
        return LogOptions::defaults()
            ->logOnly(['user_id', 'date_from', 'date_to', 'exam_type_ids', 'patients_count', 'exams_count', 'model', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('health_campaign');
    }
}
