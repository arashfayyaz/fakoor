<div>
    <x-site.breadcrumbs :data="$page_address" title="{{ $package->title }}" />

    <section class="course-details-area pb-20px">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 pb-5">
                    <div class="course-details-content-wrap mt-5">
                        <img src="{{ asset($package->image) }}" class="w-100 rounded mb-4" alt="{{ $package->title }}">
                        <h2 class="fs-30 font-weight-semi-bold pb-3">{{ $package->title }}</h2>
                        <div class="course-overview-card">
                            {!! $package->descriptions !!}
                        </div>

                        <div class="course-overview-card pt-4">
                            <h3 class="fs-24 font-weight-semi-bold pb-4">آزمون های این پکیج</h3>
                            <div class="table-responsive">
                                <table class="table generic-table">
                                    <thead>
                                    <tr>
                                        <th>عنوان آزمون</th>
                                        <th>حداقل نمره قبولی</th>
                                        <th>بارم</th>
                                        <th>گواهینامه</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($package->quizzes as $quiz)
                                        <tr>
                                            <td>{{ $quiz->name }}</td>
                                            <td>{{ $quiz->minimum_score }}</td>
                                            <td>{{ $quiz->total_score }}</td>
                                            <td>{{ $quiz->certificate->title ?? 'ندارد' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar" style="margin-top: 20px;">
                        <div class="card card-item">
                            <div class="card-body">
                                <div class="preview-course-feature-content pt-1">
                                    <div>
                                        @if($package->has_reduction && $package->base_price > 0)
                                            <p class="before-price mx-1">{{ number_format($package->base_price) }}</p>
                                            <span class="fs-35 font-weight-semi-bold text-black">{{ number_format($package->price) }} تومان</span>
                                            <p class="price-discount p-1">{{ $package->reduction_percent }} درصد تخفیف</p>
                                        @elseif($package->price == 0)
                                            <span class="fs-35 font-weight-semi-bold text-black">رایگان</span>
                                        @else
                                            <span class="fs-35 font-weight-semi-bold text-black">{{ number_format($package->price) }} تومان</span>
                                        @endif
                                    </div>
                                    <div class="buy-course-btn-box mt-4">
                                        @if($package->sellable)
                                            <button wire:click="addToCart()" type="button" class="btn theme-btn w-100 mb-2">
                                                <i class="la la-shopping-cart fs-18 mr-1"></i> افزودن به سبد خرید
                                            </button>
                                        @else
                                            <button disabled type="button" class="btn theme-btn w-100 mb-2">فروش این پکیج فعال نیست</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-item">
                            <div class="card-body">
                                <h3 class="card-title fs-18 pb-2">ویژگی های آزمون</h3>
                                <div class="divider"><span></span></div>
                                <ul class="generic-list-item generic-list-item-flash">
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span><i class="la la-pen mr-2 text-color"></i>تعداد آزمون</span> {{ $package->quiz_count }}
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span><i class="la la-refresh mr-2 text-color"></i>دفعات مجاز شرکت</span> {{ $package->enter_count }}
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span><i class="la la-certificate mr-2 text-color"></i>گواهینامه</span> مطابق آزمون
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span><i class="la la-magic mr-2 text-color"></i>هوش مصنوعی</span> بله
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
