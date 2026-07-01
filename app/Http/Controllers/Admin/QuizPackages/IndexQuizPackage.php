<?php

namespace App\Http\Controllers\Admin\QuizPackages;

use App\Http\Controllers\BaseComponent;
use App\Models\QuizPackage;
use Livewire\WithPagination;

class IndexQuizPackage extends BaseComponent
{
    use WithPagination;

    public ?string $status = null;
    public string $placeholder = 'عنوان پکیج آزمون';

    public function mount()
    {
        $this->data['status'] = QuizPackage::getStatus();
    }

    public function render()
    {
        $this->authorizing('show_quizzes');

        $packages = QuizPackage::latest('id')
            ->search($this->search)
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->withCount('quizzes')
            ->paginate($this->per_page);

        return view('admin.quiz-packages.index-quiz-package', ['packages' => $packages])
            ->extends('admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorizing('delete_quizzes');
        QuizPackage::destroy($id);
    }
}
