<?php

namespace App\Http\Controllers\Site\QuizPackages;

use App\Http\Controllers\BaseComponent;
use App\Models\QuizPackage;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Livewire\WithPagination;

class IndexQuizPackage extends BaseComponent
{
    use WithPagination;

    protected $queryString = ['q'];

    public ?string $q = null;

    public function mount(SettingRepositoryInterface $settingRepository)
    {
        SEOMeta::setTitle($settingRepository->getRow('title').' آزمون ها ');
        SEOMeta::setDescription($settingRepository->getRow('seoDescription'));
        SEOMeta::addKeyword($settingRepository->getRow('seoKeyword', []));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle($settingRepository->getRow('title').' آزمون ها ');
        OpenGraph::setDescription($settingRepository->getRow('seoDescription'));
        TwitterCard::setTitle($settingRepository->getRow('title').' آزمون ها ');
        TwitterCard::setDescription($settingRepository->getRow('seoDescription'));
        JsonLd::setTitle($settingRepository->getRow('title').' آزمون ها ');
        JsonLd::setDescription($settingRepository->getRow('seoDescription'));
        JsonLd::addImage(asset($settingRepository->getRow('logo')));

        $this->page_address = [
            'home' => ['link' => route('home'), 'label' => 'فکور'],
            'exams' => ['link' => '', 'label' => 'آزمون ها'],
        ];
    }

    public function render()
    {
        $packages = QuizPackage::published()
            ->withCount('quizzes')
            ->search($this->q)
            ->latest('id')
            ->paginate(9);

        return view('site.quiz-packages.index-quiz-package', ['packages' => $packages])
            ->extends('site.layouts.site.site');
    }
}
