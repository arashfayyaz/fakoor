@props(['data'])
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
</section>
