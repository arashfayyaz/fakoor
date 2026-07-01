<?php

namespace App\Http\Controllers\Admin\QuizPackages;

use App\Enums\ReductionEnum;
use App\Http\Controllers\BaseComponent;
use App\Models\Quiz;
use App\Models\QuizPackage;

class StoreQuizPackage extends BaseComponent
{
    public QuizPackage $package;

    public $header, $title, $slug, $image, $descriptions, $const_price = 0, $reduction_type,
        $reduction_value = 0, $start_at, $expire_at, $enter_count = 1, $sellable = true,
        $status = QuizPackage::STATUS_PUBLISHED, $seo_keywords, $seo_description;

    public array $quizzes = [];

    public function mount($action, $id = null)
    {
        $this->authorizing('show_quizzes');
        $this->set_mode($action);

        $this->data['status'] = QuizPackage::getStatus();
        $this->data['reduction'] = ReductionEnum::getType();
        $this->data['quizzes'] = Quiz::latest('id')->get();

        if ($this->mode == self::UPDATE_MODE) {
            $this->package = QuizPackage::with('quizzes')->findOrFail($id);
            $this->header = $this->package->title;
            $this->title = $this->package->title;
            $this->slug = $this->package->slug;
            $this->image = $this->package->image;
            $this->descriptions = $this->package->descriptions;
            $this->const_price = $this->package->const_price;
            $this->reduction_type = $this->package->reduction_type;
            $this->reduction_value = $this->package->reduction_value;
            $this->start_at = $this->dateConverter($this->package->start_at);
            $this->expire_at = $this->dateConverter($this->package->expire_at);
            $this->enter_count = $this->package->enter_count;
            $this->sellable = $this->package->sellable;
            $this->status = $this->package->status;
            $this->seo_keywords = $this->package->seo_keywords;
            $this->seo_description = $this->package->seo_description;
            $this->quizzes = $this->package->quizzes->pluck('id', 'id')->toArray();
        } elseif ($this->mode == self::CREATE_MODE) {
            $this->header = 'پکیج آزمون جدید';
            $this->package = new QuizPackage();
        } else abort(404);
    }

    public function render()
    {
        return view('admin.quiz-packages.store-quiz-package')->extends('admin.layouts.admin');
    }

    public function store()
    {
        $this->authorizing('edit_quizzes');

        $model = $this->mode == self::UPDATE_MODE ? $this->package : new QuizPackage();
        $this->saveInDataBase($model);

        if ($this->mode == self::CREATE_MODE) {
            $this->reset([
                'title', 'slug', 'image', 'descriptions', 'const_price', 'reduction_type',
                'reduction_value', 'start_at', 'expire_at', 'enter_count', 'sellable',
                'status', 'seo_keywords', 'seo_description', 'quizzes'
            ]);
            $this->status = QuizPackage::STATUS_PUBLISHED;
            $this->sellable = true;
            $this->enter_count = 1;
            $this->const_price = 0;
            $this->reduction_value = 0;
        }
    }

    private function saveInDataBase(QuizPackage $model)
    {
        $this->reduction_type = $this->emptyToNull($this->reduction_type);
        $this->start_at = $this->dateConverter($this->start_at, 'm');
        $this->expire_at = $this->dateConverter($this->expire_at, 'm');

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:quiz_packages,slug,'.($this->package->id ?? 0)],
            'image' => ['required', 'string', 'max:255'],
            'descriptions' => ['nullable', 'string'],
            'const_price' => ['required', 'numeric', 'between:0,999999999999999999'],
            'reduction_type' => ['nullable', 'in:'.implode(',', array_keys(ReductionEnum::getType()))],
            'reduction_value' => ['required', 'numeric', 'between:0,999999999999999999'],
            'start_at' => ['nullable', 'date'],
            'expire_at' => ['nullable', 'date'],
            'enter_count' => ['required', 'integer', 'between:1,999999'],
            'sellable' => ['boolean'],
            'status' => ['required', 'in:'.implode(',', array_keys(QuizPackage::getStatus()))],
            'seo_keywords' => ['nullable', 'string', 'max:5200'],
            'seo_description' => ['nullable', 'string', 'max:5200'],
            'quizzes' => ['array', 'min:1'],
            'quizzes.*' => ['nullable', 'exists:quizzes,id'],
        ], [], [
            'title' => 'عنوان',
            'slug' => 'نام مستعار',
            'image' => 'تصویر',
            'descriptions' => 'توضیحات',
            'const_price' => 'قیمت',
            'reduction_type' => 'نوع تخفیف',
            'reduction_value' => 'مقدار تخفیف',
            'start_at' => 'شروع تخفیف',
            'expire_at' => 'پایان تخفیف',
            'enter_count' => 'تعداد دفعات مجاز شرکت',
            'sellable' => 'قابل خرید',
            'status' => 'وضعیت',
            'seo_keywords' => 'کلمات سئو',
            'seo_description' => 'توضیحات سئو',
            'quizzes' => 'آزمون ها',
        ]);

        $model->title = $this->title;
        $model->slug = $this->slug;
        $model->image = $this->image;
        $model->descriptions = $this->descriptions;
        $model->const_price = $this->const_price;
        $model->reduction_type = $this->reduction_type;
        $model->reduction_value = $this->reduction_value;
        $model->start_at = $this->start_at;
        $model->expire_at = $this->expire_at;
        $model->enter_count = $this->enter_count;
        $model->sellable = $this->sellable;
        $model->status = $this->status;
        $model->seo_keywords = $this->seo_keywords;
        $model->seo_description = $this->seo_description;
        $model->save();

        $model->quizzes()->sync(array_filter($this->quizzes));

        return $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->authorizing('delete_quizzes');
        $this->package->delete();

        return redirect()->route('admin.quiz-package');
    }
}
