<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $exam_type_field_id
 * @property string|null $sex
 * @property int|null $age_min
 * @property int|null $age_max
 * @property float|null $min_value
 * @property float|null $max_value
 * @property-read string $criteria_label
 * @property-read string $range_label
 */
class ExamTypeFieldReference extends Model
{
    use LogsActivity;

    public const SEX_LABELS = [
        'male' => 'Masculino',
        'female' => 'Feminino',
    ];

    private const ANY_SEX_LABEL = 'Ambos os sexos';

    private const ANY_AGE_LABEL = 'todas as idades';

    private const EMPTY_RANGE_LABEL = '—';

    private const VALUE_DECIMALS = 4;

    protected $fillable = [
        'exam_type_field_id',
        'sex',
        'age_min',
        'age_max',
        'min_value',
        'max_value',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
        'min_value' => 'float',
        'max_value' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['criteria_label', 'range_label'];

    public function examTypeField(): BelongsTo
    {
        return $this->belongsTo(ExamTypeField::class);
    }

    /**
     * Indica se a referência se aplica a um paciente com o sexo e a idade informados.
     * Critérios nulos na referência aceitam qualquer valor; já um paciente sem
     * sexo/idade conhecidos só se encaixa em referências sem esse critério.
     */
    public function appliesTo(?string $sex, ?int $age): bool
    {
        if ($this->sex !== null && $this->sex !== $sex) {
            return false;
        }

        if ($this->age_min !== null && ($age === null || $age < $this->age_min)) {
            return false;
        }

        if ($this->age_max !== null && ($age === null || $age > $this->age_max)) {
            return false;
        }

        return true;
    }

    /**
     * Quanto mais critérios definidos, mais específica (e prioritária) é a referência.
     */
    public function specificity(): int
    {
        return (int) ($this->sex !== null)
            + (int) ($this->age_min !== null)
            + (int) ($this->age_max !== null);
    }

    public function getCriteriaLabelAttribute(): string
    {
        $sexLabel = self::SEX_LABELS[$this->sex] ?? self::ANY_SEX_LABEL;

        return "{$sexLabel}, {$this->ageLabel()}";
    }

    public function getRangeLabelAttribute(): string
    {
        $min = $this->formatValue($this->min_value);
        $max = $this->formatValue($this->max_value);

        if ($min !== null && $max !== null) {
            return "{$min} a {$max}";
        }

        if ($min !== null) {
            return "≥ {$min}";
        }

        if ($max !== null) {
            return "≤ {$max}";
        }

        return self::EMPTY_RANGE_LABEL;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('exam_type_field_reference');
    }

    private function ageLabel(): string
    {
        if ($this->age_min !== null && $this->age_max !== null) {
            return "{$this->age_min} a {$this->age_max} anos";
        }

        if ($this->age_min !== null) {
            return "a partir de {$this->age_min} anos";
        }

        if ($this->age_max !== null) {
            return "até {$this->age_max} anos";
        }

        return self::ANY_AGE_LABEL;
    }

    private function formatValue(?float $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $formatted = number_format($value, self::VALUE_DECIMALS, ',', '.');

        return rtrim(rtrim($formatted, '0'), ',');
    }
}
