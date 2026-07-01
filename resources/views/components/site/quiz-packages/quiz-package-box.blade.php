@props(['item'])
<div {{ $attributes }}>
    <div class="card card-item card-preview" style="height: 25rem;">
        <div class="card-image">
            <a href="{{ route('exam', $item->slug) }}" class="d-block">
                <img class="card-img-top" src="{{ asset($item->image) }}" alt="{{ $item->title }}" />
            </a>
            <div class="course-badge-labels">
                <div class="course-badge">آزمون</div>
                @if($item->has_reduction && $item->base_price > 0)
                    <div class="course-badge blue">٪{{ $item->reduction_percent }}-</div>
                @endif
                <div class="course-badge green">{{ $item->quiz_count }} آزمون</div>
            </div>
        </div>
        <div class="card-body">
            <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">پکیج آزمون</h6>
            <h5 class="card-title">
                <a href="{{ route('exam', $item->slug) }}" title="{{ $item->title }}">{{ $item->title }}</a>
            </h5>
            <div class="rating-wrap d-flex align-items-center py-2">
                <div class="review-stars">
                    <span class="la la-pen"></span>
                    <span class="fs-14 pr-1">{{ $item->enter_count }} بار امکان شرکت</span>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                @if($item->has_reduction && $item->base_price > 0)
                    <p class="card-price text-black font-weight-bold">{{ number_format($item->price) }} تومان
                        <br/>
                        <span class="before-price font-weight-medium">{{ number_format($item->base_price) }} تومان</span>
                    </p>
                @elseif($item->base_price == 0 || $item->price == 0)
                    <p class="card-price text-black font-weight-bold">رایگان</p>
                @else
                    <p class="card-price text-black font-weight-bold">{{ number_format($item->price) }} تومان</p>
                @endif
            </div>
        </div>
    </div>
</div>
