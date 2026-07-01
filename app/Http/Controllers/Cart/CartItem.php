<?php

namespace App\Http\Controllers\Cart;

use App\Models\Course;
use App\Models\QuizPackage;

class CartItem
{
    public const TYPE_COURSE = 'course';
    public const TYPE_QUIZ_PACKAGE = 'quiz_package';

    public $id;
    public $rowId;
    public $type;
    public $title;
    public $image;
    public $quantity;
    public $price;
    public $basePrice;
    public $discount;

    public function __construct($product, ?string $type = null)
    {
        $this->type = $type ?? $this->resolveType($product);
        $this->id = $product->id;
        $this->rowId = "{$this->type}:{$this->id}";
        $this->title = $product->title;
        $this->image = $product->image;
        $this->price = $product->price;
        $this->basePrice = $product->basePrice;
    }

    private function resolveType($product): string
    {
        if ($product instanceof QuizPackage) {
            return self::TYPE_QUIZ_PACKAGE;
        }

        return self::TYPE_COURSE;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_QUIZ_PACKAGE => 'پکیج آزمون',
            default => 'دوره آموزشی',
        };
    }

    public function price()
    {
        return ($this->basePrice) ;
    }


    public function discount()
    {
        return ($this->basePrice - $this->price);
    }

    public function total()
    {
        return $this->price() - $this->discount();
    }
}
