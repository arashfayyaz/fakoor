<?php

namespace App\Models;

use App\Enums\OrderEnum;
use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\Jalalian;

/**
 * @property mixed status
 * @property mixed id
 * @property mixed created_at
 * @property mixed $type
 * @method static findOrFail($id)
 * @method static whereBetween(string $string, string[] $array)
 * @method static create(array $data)
 */
class OrderDetail extends Model
{
    use HasFactory , Searchable , SoftDeletes;

    protected $guarded = ['id'];

    protected array $searchAbleColumns = ['id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quizPackage(): BelongsTo
    {
        return $this->belongsTo(QuizPackage::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return OrderEnum::getStatus()[$this->status];
    }

    public function getTrackingCodeAttribute(): string
    {
        return 'FL-D-'.($this->id + Order::CHANGE_ID);
    }

    public function getProductDataAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    public function getProductTitleAttribute(): string
    {
        return $this->product_data['title'] ?? $this->course?->title ?? $this->quizPackage?->title ?? '';
    }

    public function getProductUrlAttribute(): ?string
    {
        if (!is_null($this->course)) {
            return route('course', $this->course->slug);
        }

        if (!is_null($this->quizPackage)) {
            return route('exam', $this->quizPackage->slug);
        }

        return null;
    }

    public function getDateAttribute(): string
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }
}
