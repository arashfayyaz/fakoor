{{-- @props(['data'])
    <div class="container mt-5">
        <div class="courses-box">
            <h3 class="courses-title">آموزش‌های تخصصی ما در بجنورد</h3>

            <div class="course-list">

                <a href="{{ route('courses') }}" class="course-card">
                    <img src="/images/icdl.jpg" alt="آموزش ICDL">
                    <div class="course-info">
                        <h5>آموزش ICDL</h5>
                        <p>یادگیری مهارت‌های هفتگانه کامپیوتر</p>
                    </div>
                </a>

                <a href="{{ route('courses') }}" class="course-card">
                    <img src="/images/webdesign.jpg" alt="آموزش طراحی سایت">
                    <div class="course-info">
                        <h5>آموزش طراحی سایت</h5>
                        <p>طراحی سایت با HTML ،CSS و Laravel</p>
                    </div>
                </a>

                <a href="{{ route('courses') }}" class="course-card">
                    <img src="../images/programming.jpg" alt="آموزش برنامه نویسی">
                    <div class="course-info">
                        <h5>آموزش برنامه‌نویسی</h5>
                        <p>آموزش تخصصی برنامه نویسی از مقدماتی تا پیشرفته</p>
                    </div>
                </a>

            </div>
        </div>
    </div>
<section class="blog-area pt-5 bg-gray overflow-hidden">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <div class="category-content-wrap">
                    <div class="section-heading ">
                        <h5 class="ribbon ribbon-lg mb-2">دسته بندی ها</h5>
                        <h2 class="section__title">{{$data['title']}}</h2>
                        <span class="section-divider"></span>
                    </div><!-- end section-heading -->
                </div>
            </div><!-- end col-lg-9 -->
            <div class="col-lg-3">
                <div class="category-btn-box text-left">
                    @if(!empty($data['moreLink']))
                        <a href="{{$data['moreLink']}}" class="btn theme-btn">همه دسته بندی ها <i class="la la-arrow-left icon ml-1"></i></a>
                    @endif
                </div><!-- end category-btn-box-->
            </div><!-- end col-lg-3 -->
        </div>
        <div class="blog-post-carousel owl-action-styled half-shape mt-30px">
            @foreach($data['content'] as $item)
                <x-site.categories.category-box :item="$item" />
            @endforeach
        </div>
    </div>
</section> --}}
@props(['data'])
@php
    $course1Id = App\Models\Setting::where('name', 'home_course_1')->value('value');
    $course2Id = App\Models\Setting::where('name', 'home_course_2')->value('value');
    $course3Id = App\Models\Setting::where('name', 'home_course_3')->value('value');
    
    $course1 = $course1Id ? App\Models\Course::find($course1Id) : null;
    $course2 = $course2Id ? App\Models\Course::find($course2Id) : null;
    $course3 = $course3Id ? App\Models\Course::find($course3Id) : null;
@endphp

<div class="container mt-5">
    <div class="courses-box">
        <h3 class="courses-title">آموزش‌های تخصصی ما در بجنورد</h3>
        <div class="course-list">
        @if(!$course1 && !$course2 && !$course3)
    <p class="text-center text-muted">دوره‌ای انتخاب نشده است</p>
@endif

            @if($course1)
            <a href="{{ route('course', $course1->slug) }}" class="course-card">
                <img src="{{ $course1->image }}" alt="{{ $course1->title }}">
                <div class="course-info">
                    <h5>{{ $course1->title }}</h5>
                    <p>{{ $course1->sub_title }}</p>
                </div>
            </a>
            @endif

            @if($course2)
            <a href="{{ route('course', $course2->slug) }}" class="course-card">
                <img src="{{ $course2->image }}" alt="{{ $course2->title }}">
                <div class="course-info">
                    <h5>{{ $course2->title }}</h5>
                    <p>{{ $course2->sub_title }}</p>
                </div>
            </a>
            @endif

            @if($course3)
            <a href="{{ route('course', $course3->slug) }}" class="course-card">
                <img src="{{ $course3->image }}" alt="{{ $course3->title }}">
                <div class="course-info">
                    <h5>{{ $course3->title }}</h5>
                    <p>{{ $course3->sub_title }}</p>
                </div>
            </a>
            @endif

        </div>
    </div>
</div>

<section class="blog-area pt-5 bg-gray overflow-hidden">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <div class="category-content-wrap">
                    <div class="section-heading">
                        <h5 class="ribbon ribbon-lg mb-2">دسته بندی ها</h5>
                        <h2 class="section__title">{{$data['title']}}</h2>
                        <span class="section-divider"></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="category-btn-box text-left">
                    @if(!empty($data['moreLink']))
                        <a href="{{$data['moreLink']}}" class="btn theme-btn">همه دسته بندی ها <i class="la la-arrow-left icon ml-1"></i></a>
                    @endif
                </div>
            </div>
        </div>
        <div class="blog-post-carousel owl-action-styled half-shape mt-30px">
            @foreach($data['content'] as $item)
                <x-site.categories.category-box :item="$item" />
            @endforeach
        </div>
    </div>
</section>