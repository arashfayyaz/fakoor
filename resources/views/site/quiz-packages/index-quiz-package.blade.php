<div>
    <x-site.breadcrumbs :data="$page_address" title="آزمون ها" />

    <section class="course-area">
        <div class="container">
            <div class="filter-bar mb-4">
                <div class="filter-bar-inner d-flex flex-wrap align-items-center justify-content-between">
                    <p class="fs-14">ما <span class="text-black">{{ $packages->count() }}</span> پکیج آزمون برای شما پیدا کردیم</p>
                    <form wire:submit.prevent="$refresh" class="d-flex flex-wrap align-items-center">
                        <div class="input-group">
                            <input wire:model.defer="q" class="form-control form--control" type="text" placeholder="جستجوی آزمون" />
                            <div class="input-group-append">
                                <button class="btn theme-btn" type="submit"><i class="la la-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(sizeof($packages) > 0)
                <div class="row">
                    @foreach($packages as $item)
                        <div class="col-lg-4 responsive-column-half">
                            <x-site.quiz-packages.quiz-package-box :item="$item" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center mb-3">
                    <img class="mx-auto no-date d-block mt-5" src="{{ asset('site/svg/no-data.svg') }}" alt="">
                    <h5 class="mt-3">هیچ آزمونی برای نمایش وجود ندارد.</h5>
                </div>
            @endif

            {{$packages->links('site.includes.paginate')}}
        </div>
    </section>
</div>
