<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\ActivityLogLabels;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'reported_at',
        'latitude',
        'longitude',
        'location_name',
        'photo_path',
        'attachment_path',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reported_at' => 'immutable_date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'reported_at',
                'latitude',
                'longitude',
                'location_name',
                'photo_path',
                'attachment_path',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName): string => ActivityLogLabels::description($eventName, self::class),
            );
    }
}
