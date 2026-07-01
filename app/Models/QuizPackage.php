<?php

namespace App\Models;

use App\Enums\ReductionEnum;
use App\Traits\Admin\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizPackage extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $guarded = ['id'];

    protected array $searchAbleColumns = ['title', 'slug', 'descriptions'];

    public static function getStatus(): array
    {
        return [
            self::STATUS_PUBLISHED => 'منتشر شده',
            self::STATUS_DRAFT => 'پیش نویس',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)->where('sellable', true);
    }

    public function setImageAttribute($value)
    {
        $this->attributes['image'] = str_replace(env('APP_URL'), '', $value);
    }

    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'quiz_package_quiz');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::getStatus()[$this->status] ?? 'نامشخص'
        );
    }

    protected function quizCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quizzes()->count()
        );
    }

    public function getBasePriceAttribute()
    {
        return $this->const_price;
    }

    public function getHasReductionAttribute(): bool
    {
        $reduction = $this->reduction_value;
        $now = Carbon::now();
        $start = $this->start_at ?? null;
        $end = $this->expire_at ?? null;

        if ($reduction > 0 && in_array($this->reduction_type, [ReductionEnum::PERCENT, ReductionEnum::AMOUNT])) {
            if ($start == null && $end == null) {
                return true;
            } elseif ($start <> null && $end == null) {
                return !($now->diff(Carbon::make($start))->format('%r%h') > 0);
            } elseif ($start == null && $end <> null) {
                return !($now->diff(Carbon::make($end))->format('%r%h') <= 0);
            } elseif ($start <> null && $end <> null) {
                return $now->diff(Carbon::make($start))->format('%r%h') <= 0
                    && $now->diff(Carbon::make($end))->format('%r%h') >= 0;
            }
        }

        return false;
    }

    public function getPriceAttribute()
    {
        $price = $this->base_price;

        if ($this->has_reduction) {
            $price = match ($this->reduction_type) {
                ReductionEnum::PERCENT => $price - $price * ($this->reduction_value / 100),
                ReductionEnum::AMOUNT => $price - $this->reduction_value,
                default => $price,
            };
        }

        return max($price, 0);
    }

    public function getReductionAmountAttribute()
    {
        return $this->base_price - $this->price;
    }

    public function getReductionPercentAttribute(): float|int
    {
        return $this->base_price > 0 ? round((($this->base_price - $this->price) / $this->base_price) * 100) : 0;
    }
}
