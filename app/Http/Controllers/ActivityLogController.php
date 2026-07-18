<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogTranslator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    private const LOGS_PER_PAGE = 20;

    private const HIDDEN_FIELDS = ['password', 'remember_token'];

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', ''),
            'log_name' => $request->input('log_name', ''),
            'event' => $request->input('event', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to' => $request->input('date_to', ''),
        ];

        $logs = $this->buildLogsQuery($filters)
            ->paginate(self::LOGS_PER_PAGE)
            ->withQueryString()
            ->through(fn (Activity $log): array => $this->transformLogSummary($log));

        return Inertia::render('activity-logs/index', [
            'logs' => $logs,
            'filters' => $filters,
            'logNameOptions' => $this->getLogNameOptions(),
            'eventOptions' => $this->getEventOptions(),
        ]);
    }

    public function show($id): Response
    {
        $log = Activity::with(['causer', 'subject'])->findOrFail($id);

        return Inertia::render('activity-logs/show', [
            'log' => [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'log_name_label' => ActivityLogTranslator::translateLogName($log->log_name ?? ''),
                'event' => $log->event,
                'event_label' => ActivityLogTranslator::translateEvent($log->event ?? ''),
                'description' => $log->description,
                'causer' => $this->transformCauser($log),
                'subject_type' => $log->subject_type,
                'subject_type_label' => $log->subject_type
                    ? ActivityLogTranslator::translateModelName($log->subject_type)
                    : null,
                'subject_id' => $log->subject_id,
                'created_at' => optional($log->created_at)->toIso8601String(),
                'diff' => $this->buildDiff($log),
            ],
        ]);
    }

    private function buildLogsQuery(array $filters)
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if (! empty($filters['log_name'])) {
            $query->where('log_name', $filters['log_name']);
        }

        if (! empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($causerQuery) use ($search) {
                        $causerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function transformLogSummary(Activity $log): array
    {
        return [
            'id' => $log->id,
            'log_name' => $log->log_name,
            'log_name_label' => ActivityLogTranslator::translateLogName($log->log_name ?? ''),
            'event' => $log->event,
            'event_label' => ActivityLogTranslator::translateEvent($log->event ?? ''),
            'causer' => $this->transformCauser($log),
            'subject_type_label' => $log->subject_type
                ? ActivityLogTranslator::translateModelName($log->subject_type)
                : null,
            'subject_id' => $log->subject_id,
            'created_at' => optional($log->created_at)->toIso8601String(),
        ];
    }

    private function transformCauser(Activity $log): ?array
    {
        if (! $log->causer) {
            return null;
        }

        return [
            'name' => $log->causer->name,
            'email' => $log->causer->email,
        ];
    }

    /**
     * Monta o diff já traduzido no servidor de acordo com o tipo de evento.
     *
     * @return array{type: string, items: array<int, array<string, string>>}
     */
    private function buildDiff(Activity $log): array
    {
        $properties = $log->properties ? $log->properties->toArray() : [];
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];

        if ($log->event === 'updated' && $attributes && $old) {
            return [
                'type' => 'updated',
                'items' => $this->buildUpdatedItems($attributes, $old),
            ];
        }

        if ($log->event === 'created' && $attributes) {
            return [
                'type' => 'created',
                'items' => $this->buildValueItems($attributes),
            ];
        }

        if ($log->event === 'deleted' && $old) {
            return [
                'type' => 'deleted',
                'items' => $this->buildValueItems($old),
            ];
        }

        return ['type' => 'none', 'items' => []];
    }

    /**
     * @return array<int, array{field: string, old: string, new: string}>
     */
    private function buildUpdatedItems(array $attributes, array $old): array
    {
        $items = [];

        foreach ($attributes as $key => $newValue) {
            if (! array_key_exists($key, $old) || $old[$key] == $newValue) {
                continue;
            }

            $items[] = [
                'field' => ActivityLogTranslator::translateFieldName($key),
                'old' => ActivityLogTranslator::translateFieldValue($key, $old[$key]),
                'new' => ActivityLogTranslator::translateFieldValue($key, $newValue),
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{field: string, value: string}>
     */
    private function buildValueItems(array $values): array
    {
        $items = [];

        foreach ($values as $key => $value) {
            if (in_array($key, self::HIDDEN_FIELDS, true)) {
                continue;
            }

            $items[] = [
                'field' => ActivityLogTranslator::translateFieldName($key),
                'value' => ActivityLogTranslator::translateFieldValue($key, $value),
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getLogNameOptions(): array
    {
        return Activity::query()
            ->select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->orderBy('log_name')
            ->pluck('log_name')
            ->map(fn (string $logName): array => [
                'value' => $logName,
                'label' => ActivityLogTranslator::translateLogName($logName),
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getEventOptions(): array
    {
        return Activity::query()
            ->select('event')
            ->distinct()
            ->whereNotNull('event')
            ->orderBy('event')
            ->pluck('event')
            ->map(fn (string $event): array => [
                'value' => $event,
                'label' => ActivityLogTranslator::translateEvent($event),
            ])
            ->all();
    }
}
