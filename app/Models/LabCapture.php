<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $lab_agent_id
 * @property int|null $machine_id
 * @property int|null $exam_id
 * @property int|null $reviewed_by
 * @property string $raw_text
 * @property string $raw_hash
 * @property array<string, mixed>|null $serial_settings
 * @property \Illuminate\Support\Carbon $captured_at
 * @property \Illuminate\Support\Carbon $received_at
 * @property string $status
 * @property array<string, mixed>|null $analysis
 * @property string|null $ai_model
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property-read LabAgent|null $labAgent
 * @property-read Machine|null $machine
 * @property-read Exam|null $exam
 * @property-read User|null $reviewer
 */
class LabCapture extends Model
{
    use HasFactory, LogsActivity;

    public const STATUS_RECEIVED = 'received';

    public const STATUS_ANALYZING = 'analyzing';

    public const STATUS_ANALYZED = 'analyzed';

    public const STATUS_UNRECOGNIZED = 'unrecognized';

    public const STATUS_FAILED = 'failed';

    public const STATUS_LINKED = 'linked';

    public const STATUS_DISCARDED = 'discarded';

    public const STATUS_LABELS = [
        self::STATUS_RECEIVED => 'Recebida',
        self::STATUS_ANALYZING => 'Analisando',
        self::STATUS_ANALYZED => 'Aguardando conferência',
        self::STATUS_UNRECOGNIZED => 'Tipo não identificado',
        self::STATUS_FAILED => 'Falha na análise',
        self::STATUS_LINKED => 'Vinculada',
        self::STATUS_DISCARDED => 'Descartada',
    ];

    protected $fillable = [
        'lab_agent_id',
        'machine_id',
        'exam_id',
        'reviewed_by',
        'raw_text',
        'raw_hash',
        'serial_settings',
        'captured_at',
        'received_at',
        'status',
        'analysis',
        'ai_model',
        'error_message',
        'reviewed_at',
    ];

    protected $casts = [
        'serial_settings' => 'array',
        'analysis' => 'array',
        'captured_at' => 'datetime',
        'received_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function labAgent(): BelongsTo
    {
        return $this->belongsTo(LabAgent::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, [self::STATUS_RECEIVED, self::STATUS_ANALYZING], true);
    }

    public function isReviewable(): bool
    {
        return in_array($this->status, [
            self::STATUS_ANALYZED,
            self::STATUS_UNRECOGNIZED,
            self::STATUS_FAILED,
        ], true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['lab_agent_id', 'machine_id', 'exam_id', 'reviewed_by', 'status', 'ai_model'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('lab_capture');
    }
}
