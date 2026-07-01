<?php

namespace App\Http\Controllers\Site\QuizPackages;

use App\Http\Controllers\BaseComponent;
use App\Http\Controllers\Cart\Facades\Cart;
use App\Models\QuizPackage;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;

class SingleQuizPackage extends BaseComponent
{
    public QuizPackage $package;

    public function mount(SettingRepositoryInterface $settingRepository, $slug)
    {
        $this->package = QuizPackage::published()->with('quizzes.certificate')->where('slug', $slug)->firstOrFail();

        SEOMeta::setTitle($this->package->title);
        SEOMeta::setDescription($this->package->seo_description ?: $settingRepository->getRow('seoDescription'));
        SEOMeta::addKeyword($this->package->seo_keywords ?: $settingRepository->getRow('seoKeyword', []));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle($this->package->title);
        OpenGraph::setDescription($this->package->seo_description ?: $settingRepository->getRow('seoDescription'));
        TwitterCard::setTitle($this->package->title);
        TwitterCard::setDescription($this->package->seo_description ?: $settingRepository->getRow('seoDescription'));
        JsonLd::setTitle($this->package->title);
        JsonLd::setDescription($this->package->seo_description ?: $settingRepository->getRow('seoDescription'));
        JsonLd::addImage(asset($this->package->image));

        $this->page_address = [
            'home' => ['link' => route('home'), 'label' => 'فکور'],
            'exams' => ['link' => route('exams'), 'label' => 'آزمون ها'],
            'exam' => ['link' => '', 'label' => $this->package->title],
        ];
    }

    public function addToCart()
    {
        if ($this->package->sellable) {
            Cart::add($this->package);
            return redirect()->route('cart');
        }

        return $this->emitNotify('فروش این پکیج فعال نیست.', 'warning');
    }

    public function render()
    {
        return view('site.quiz-packages.single-quiz-package')->extends('site.layouts.site.site');
    }
}
