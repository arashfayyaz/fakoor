<div wire:init="loadCourse()">
    <x-site.courses.breadcrumbs :data="$page_address" :course="$course" />
    <section class="course-details-area pb-20px">
        <div class="container">
            <div class="row">
                {{-- ===== ستون سمت چپ (col-lg-8) ===== --}}
                <div class="col-lg-8 pb-5">
                    <div class="course-dashboard-column w-100 pt-5">
                        <div class="lecture-viewer-container col-12 p-0">
                            <div class="lecture-video-item col-12 p-0" id="videoContent">
                                @if(!is_null($api_bucket))
                                    <div class="col-12 p-0">
                                        {!! $api_bucket !!}
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-outline-primary"
                                            onclick="back_to_episode('heading{{$episode_id}}')">بازگشت به درس</button>
                                    </div>
                                @elseif(!is_null($local_video))
                                    <div class="plyr plyr--full-ui plyr--video plyr--html5 plyr--fullscreen-enabled plyr--paused">
                                        <video id="player" class="player" playsinline controls
                                            data-poster="{{asset($course->image)}}">
                                        </video>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-outline-primary"
                                            onclick="back_to_episode('heading{{$episode_id}}')">بازگشت به درس</button>
                                    </div>
                                {{-- @else
                                    <img src="{{asset($course->image)}}" class="col-12 p-0" alt="{{ $course->title }}"> --}}
                                @endif
                                <p class="text-info" wire:loading> در حال دریافت... </p>
                            </div>
                        </div>
                    </div>

                    <div class="course-details-content-wrap mt-5">
                        {!! $course->long_body !!}
                        
                        @if($course->type != \App\Enums\CourseEnum::ONLINE)
                        <div class="course-overview-card">
                            <div class="curriculum-header d-flex align-items-center justify-content-between pb-4">
                                <h3 class="fs-24 font-weight-semi-bold">محتوای دوره</h3>
                                <div class="curriculum-duration fs-15">
                                    <span class="curriculum-total__text mr-2"><strong class="text-black font-weight-semi-bold">مجموع:</strong> {{ $course->episodes->count() }} مبحث </span>
                                    <span class="curriculum-total__hours"><strong class="text-black font-weight-semi-bold">کل ساعت:</strong> {{ $course->time }}</span>
                                </div>
                            </div>
                            <div class="curriculum-content">
                                <div id="accordion" class="generic-accordion" wire:ignore>
                                    @if(sizeof($episodes) > 0)
                                        @foreach($episodes as $key => $item)
                                            {{-- کدهای اپیزودها --}}
                                        @endforeach
                                    @else
                                        <p class="alert alert-info">هنوز هیچ درسی منتشر نشده است.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ===== بخش نظرات (پرسش و پاسخ) ===== --}}
                        <div class="course-overview-card pt-4">
                            <h3 class="fs-24 font-weight-semi-bold pb-4">پرسش و پاسخ</h3>
                            
                            @auth
                            <form method="post" id="commentForm" class="row" wire:submit.prevent="new_comment">
                                <div class="input-box col-lg-12">
                                    <label class="label-text">پیام</label>
                                    <div class="form-group">
                                        @if($actionComment)
    <div class="alert alert-info alert-sm d-flex justify-content-between align-items-center mb-2 p-2">
        <small><i class="la la-reply"></i> در حال پاسخ دادن</small>
        <button type="button" class="btn btn-sm btn-link p-0"
            wire:click="$set('actionComment', null)">
            <i class="la la-times"></i> لغو
        </button>
    </div>
@endif
                                        {{-- <textarea wire:model.defer="comment" class="form-control form--control pl-3"
                                            name="message" placeholder="پیام بنویس" rows="5"></textarea> --}}
                                            <textarea wire:model.defer="comment" ... 
    placeholder="{{ $actionComment ? 'پاسخ خود را بنویسید...' : 'پیام بنویس' }}" 
    rows="5"></textarea>
                                    </div>
                                    @error('comment')
                                        <span class="invalid-feedback d-block">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="btn-box col-lg-12">
                                    {{-- <button class="btn theme-btn" type="submit">ارسال دیدگاه</button> --}}
                                    <button class="btn theme-btn" type="submit">
    {{ $actionComment ? 'ارسال پاسخ' : 'ارسال دیدگاه' }}
</button>
                                </div>
                            </form>
                            @else
                                <p class="text-info">برای ثبت دیدگاه ابتدا ثبت نام کنید</p>
                            @endif

                            <div class="review-wrap">
                                @if(sizeof($comments) > 0)
                                    @for($i=0; $i < $commentCount; $i++)
                                        @isset($comments[$i])
                                            <div class="media media-card shadow-sm p-3 mb-4 bg-white rounded pb-4 mb-1">
                                                <div class="media-img mr-4 rounded-full">
                                                    <img class="rounded-full lazy" src="{{ asset($comments[$i]->user->image) }}"
                                                        data-src="{{ asset($comments[$i]->user->image) }}"
                                                        alt="{{ $comments[$i]->user->name }}" />
                                                </div>
                                                <div class="media-body">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between pb-1">
                                                        <h5>{{ $comments[$i]->user->name }} 
                                                            {{ $comments[$i]->user->id == ($course->teacher->id ?? 0) ? " (مدرس) " : '' }}
                                                        </h5>
                                                    </div>
                                                    <span class="d-block lh-18 py-2">{{ $comments[$i]->created_at->diffForHumans() }}</span>
                                                    <p class="pb-2">{!! $comments[$i]->content !!}</p>
                                                    <div class="helpful-action">
                                                        {{-- <button wire:click="$set('actionComment',{{$comments[$i]->id}})"
                                                            class="btn btn-outline-success goToCommentForm">پاسخ</button> --}}
                                                            <button wire:click="$set('actionComment',{{$comments[$i]->id}})"
    class="btn btn-outline-success"
    onclick="scrollToForm('commentForm', 'در حال پاسخ به: {{ $comments[$i]->user->name }}')">
    پاسخ
</button>
                                                    </div>
                                                </div>
                                            </div>
                                            @foreach($comments[$i]->childrenRecursive as $value)
                                                <div class="media media-card pb-4 shadow-sm p-3 mb-5 bg-white rounded p-3 mb-4 review-reply">
                                                    <div class="media-img mr-4 rounded-full">
                                                        <img class="rounded-full lazy" src="{{ asset($value->user->image) }}"
                                                            data-src="{{ asset($value->user->image) }}" alt="{{ $value->user->name }}" />
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="d-flex flex-wrap align-items-center justify-content-between pb-1">
                                                            <h5>{{ $value->user->name }} {{ $value->user->id == ($course->teacher->id ?? 0) ? " (مدرس) " : '' }}</h5>
                                                        </div>
                                                        <span class="d-block lh-18 py-2">{{ $value->created_at->diffForHumans() }}</span>
                                                        <p class="pb-2">{!! $value->content !!}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($i != count($comments) - 1)
                                                <hr>
                                            @endif
                                        @endisset
                                    @endfor
                                @else
                                    <p class="alert alert-info">هیچ پرسش و پاسخی ثبت نشده است</p>
                                @endif
                            </div>
                            <div class="see-more-review-btn text-center">
                                @if($commentCount < count($comments))
                                    <button type="button" wire:click="moreComment()"
                                        class="btn theme-btn theme-btn-transparent">بارگیری نظرات بیشتر</button>
                                @endif
                            </div>
                        </div>

                        {{-- ===== بخش نظرات هنرجویان (جدید - اینجا قرار میگیره) ===== --}}
                        <div class="course-overview-card pt-4">
                            <h3 class="fs-24 font-weight-semi-bold pb-4">نظرات هنرجویان</h3>
                            
                            @auth
                                @if(auth()->user()->hasCourse($course->id))
                                    <form method="post" id="studentCommentForm" class="row" wire:submit.prevent="new_student_comment">
                                        <div class="input-box col-lg-12">
                                            <label class="label-text">نظر خود را به عنوان هنرجوی این دوره بنویسید</label>
                                            <div class="form-group">
                                                @if($actionStudentComment)
    <div class="alert alert-info alert-sm d-flex justify-content-between align-items-center mb-2 p-2">
        <small><i class="la la-reply"></i> در حال پاسخ دادن</small>
        <button type="button" class="btn btn-sm btn-link p-0"
            wire:click="$set('actionStudentComment', null)">
            <i class="la la-times"></i> لغو
        </button>
    </div>
@endif
                                                {{-- <textarea wire:model.defer="student_comment" class="form-control form--control pl-3"
                                                    name="student_message" placeholder="تجربه خود را از این دوره با دیگران به اشتراک بگذارید..." rows="5"></textarea> --}}
                                                    <textarea wire:model.defer="student_comment" ...
    placeholder="{{ $actionStudentComment ? 'پاسخ خود را بنویسید...' : 'تجربه خود را از این دوره با دیگران به اشتراک بگذارید...' }}"
    rows="5"></textarea>
                                            </div>
                                            @error('student_comment')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="btn-box col-lg-12">
                                            {{-- <button class="btn theme-btn" type="submit">
                                                <i class="la la-comment-o"></i> ارسال نظر هنرجویی
                                            </button> --}}
                                            <button class="btn theme-btn" type="submit">
    <i class="la la-{{ $actionStudentComment ? 'reply' : 'comment-o' }}"></i>
    {{ $actionStudentComment ? 'ارسال پاسخ' : 'ارسال نظر هنرجویی' }}
</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-info">
                                        <i class="la la-info-circle"></i>
                                        برای ثبت نظر به عنوان هنرجو، ابتدا این دوره را خریداری کنید
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="la la-lock"></i>
                                    برای ثبت نظر به عنوان هنرجو، ابتدا <a href="{{ route('auth') }}">وارد شوید</a>
                                </div>
                            @endauth

                            <div class="review-wrap">
                                @if(sizeof($student_comments) > 0)
                                    @for($i=0; $i < $studentCommentCount; $i++)
                                        @isset($student_comments[$i])
                                            <div class="media media-card shadow-sm p-3 mb-4 bg-white rounded pb-4 mb-1">
                                                <div class="media-img mr-4 rounded-full">
                                                    <img class="rounded-full lazy" src="{{ asset($student_comments[$i]->user->image) }}"
                                                        data-src="{{ asset($student_comments[$i]->user->image) }}"
                                                        alt="{{ $student_comments[$i]->user->name }}" />
                                                </div>
                                                <div class="media-body">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between pb-1">
                                                        <h5>
                                                            {{ $student_comments[$i]->user->name }}
                                                            @if($student_comments[$i]->user->id == ($course->teacher->id ?? 0))
                                                                <span class="badge badge-primary">(مدرس)</span>
                                                            @endif
                                                            @if(auth()->check() && auth()->user()->hasCourse($course->id))
                                                                <span class="badge badge-success">(هنرجو)</span>
                                                            @endif
                                                        </h5>
                                                        <small class="text-muted">{{ $student_comments[$i]->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="pb-2">{!! $student_comments[$i]->content !!}</p>
                                                    <div class="helpful-action">
                                                        {{-- <button wire:click="$set('actionStudentComment', {{ $student_comments[$i]->id }})"
                                                            class="btn btn-outline-success goToStudentCommentForm">
                                                            <i class="la la-reply"></i> پاسخ
                                                        </button> --}}
                                                        <button wire:click="$set('actionStudentComment', {{ $student_comments[$i]->id }})"
    class="btn btn-outline-success"
    onclick="scrollToForm('studentCommentForm', 'در حال پاسخ به: {{ $student_comments[$i]->user->name }}')">
    <i class="la la-reply"></i> پاسخ
</button>
                                                    </div>
                                                </div>
                                            </div>
                                            @foreach($student_comments[$i]->childrenRecursive as $value)
                                                <div class="media media-card pb-4 shadow-sm p-3 mb-5 bg-white rounded p-3 mb-4 review-reply">
                                                    <div class="media-img mr-4 rounded-full">
                                                        <img class="rounded-full lazy" src="{{ asset($value->user->image) }}"
                                                            data-src="{{ asset($value->user->image) }}" alt="{{ $value->user->name }}" />
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="d-flex flex-wrap align-items-center justify-content-between pb-1">
                                                            <h5>
                                                                {{ $value->user->name }}
                                                                @if($value->user->id == ($course->teacher->id ?? 0))
                                                                    <span class="badge badge-primary">(مدرس)</span>
                                                                @endif
                                                            </h5>
                                                            <small class="text-muted">{{ $value->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="pb-2">{!! $value->content !!}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($i != count($student_comments) - 1)
                                                <hr>
                                            @endif
                                        @endisset
                                    @endfor
                                @else
                                    <div class="alert alert-info">
                                        <i class="la la-comment-o"></i>
                                        هنوز هیچ نظری از هنرجویان ثبت نشده است
                                    </div>
                                @endif
                            </div>
                            @if($studentCommentCount < count($student_comments))
                                <div class="see-more-review-btn text-center">
                                    <button type="button" wire:click="moreStudentComment"
                                        class="btn theme-btn theme-btn-transparent">
                                        بارگیری نظرات بیشتر هنرجویان
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- ===== پایان ستون سمت چپ ===== --}}

                {{-- ===== ستون سمت راست (col-lg-4) ===== --}}
                <div class="col-lg-4">
                    {{-- <div class="sidebar sidebar-negative"> --}}
                        <div class="sidebar" style="margin-top: 20px;">

                        {{-- اطلاعات و خرید دوره --}}
                        <div class="card card-item">
                            <div class="card-body">
                                <div class="preview-course-feature-content pt-1 mb-">
                                    <div class="">
                                        @if($course->has_reduction && $course->base_price > 0)
                                            <div class="m-0 p-0">
                                                <p class="before-price mx-1"> {{ number_format($course->base_price) }} </p>
                                                @if($course->price > 0)
                                                    <span class="fs-35 font-weight-semi-bold text-black">{{ number_format($course->price) }} تومان</span>
                                                @else
                                                    <span class="fs-35 font-weight-semi-bold text-black">رایگان</span>
                                                @endif
                                            </div>
                                            <p class="price-discount p-1">{{ $course->reduction_percent }} درصد تخفیف</p>
                                            @if(!empty($course->expire_at))
                                                <p class="preview-price-discount-text pt-4"><span class="text-color-3">{{ $course->expire_at->diffForHumans() }}</span> با این قیمت!</p>
                                            @endif
                                        @elseif($course->base_price == 0 || $course->price == 0)
                                            <span class="fs-35 font-weight-semi-bold text-black">رایگان</span>
                                        @else
                                            <span class="fs-35 font-weight-semi-bold text-black">{{ number_format($course->price) }} تومان</span>
                                        @endif
                                    </div>
                                    <div class="buy-course-btn-box mt-4">
                                        @if($course->sellable)
                                            @if ($course->price == 0)
                                                @if (auth()->check() && $user->hasCourse($course->id))
                                                    <button disabled type="button" class="btn btn-outline-success w-100 mb-2">شما در این دوره ثبت نام کرده اید</button>
                                                @else
                                                    <button wire:click="getFreeOrder()" type="button" class="btn theme-btn w-100 mb-2"><i class="la la-shopping-cart fs-18 mr-1"></i> ثبت نام در این دوره</button>
                                                @endif
                                            @else
                                                <button wire:click="addToCart()" type="button" class="btn theme-btn w-100 mb-2"><i class="la la-shopping-cart fs-18 mr-1"></i> به سبد خرید اضافه کنید</button>
                                            @endif
                                        @else
                                            <button disabled type="button" class="btn theme-btn w-100 mb-2"><i class="la la-shopping-cart fs-18 mr-1"></i> فروش این دوره به پایان رسیده است</button>
                                        @endif
                                    </div>
                                    <div class="preview-course-incentives">
                                        <h3 class="card-title fs-18 mt-2 pb-2">این دوره شامل</h3>
                                        <ul class="generic-list-item pb-3">
                                            <li><i class="la la-play-circle-o mr-2 text-color"></i>{{ $course->hours }} ساعت ویدیو آموزشی</li>
                                            <li><i class="la la-key mr-2 text-color"></i>دسترسی کامل مادام العمر</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card card-item">
            <div class="card-body">
                <h3 class="card-title fs-18 pb-2">ویژگی های دوره</h3>
                <div class="divider"><span></span></div>
                <ul class="generic-list-item generic-list-item-flash">
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-clock mr-2 text-color"></i>مدت زمان</span> {{ $course->custom_hours ?: $course->time }}
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-circle mr-2 text-color"></i>وضعیت</span> {{ $course->status_label }}
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-bolt mr-2 text-color"></i>امتحان</span>
                        {{ !is_null($course->quiz) ? 'دارد' : 'خیر' }}
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-magic mr-2 text-color"></i>هوش مصنوعی</span>
                        بله
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-eye mr-2 text-color"></i>تعداد جلسات</span> {{ $course->episodes->count() }}
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-lightbulb mr-2 text-color"></i>سطح دوره</span> {{ $course->level_label }}
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-gear mr-2 text-color"></i>نوع دوره</span> {{ $course->type_label }}
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-certificate mr-2 text-color"></i>گواهی اختصاصی آموزشگاه</span> بله
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-certificate mr-2 text-color"></i>گواهی فنی و حرفه ای</span>
                        {{ (!is_null($course->has_organization_certificate)) ? 'بله' : 'خیر' }}
                    </li>
                    @if (!empty($course->standard_code))
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-star mr-2 text-color"></i>استاندارد آموزشی</span> {{ $course->standard_code }}
                    </li>
                    @endif
                    @if(sizeof($course->organizations) > 0)
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-building mr-2 text-color"></i>سازمان ها</span>
                        <small>{{ str()->limit(implode(" و ", $course->organizations->pluck('title','id')->toArray()), 40, '...') }}</small>
                    </li>
                    @endif
                    @if(sizeof($course->executives) > 0)
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="la la-building mr-2 text-color"></i>دستگاه های اجرایی</span>
                        <small>{{ str()->limit(implode(" و ", $course->executives->pluck('title','id')->toArray()), 40, '...') }}</small>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
                        
                        {{-- سایر کارت‌های سایدبار --}}
                        {{-- ... --}}
                    </div>
                </div>
                {{-- ===== پایان ستون سمت راست ===== --}}
            </div>
        </div>
    </section>

    {{-- Modal اشتراک‌گذاری --}}
    <div class="modal fade modal-container" id="shareModal" ...>
        {{-- ... --}}
    </div>
    <script>
function scrollToForm(formId, replyText) {
    const form = document.getElementById(formId);
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // کمی صبر کن تا Livewire مقدار رو ست کنه
        setTimeout(() => {
            const textarea = form.querySelector('textarea');
            if (textarea) {
                textarea.focus();
                textarea.placeholder = replyText + '...';
            }
        }, 300);
    }
}

// بعد از هر Livewire update، placeholder رو ریست کن
document.addEventListener('livewire:update', () => {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(ta => {
        if (!ta.getAttribute('data-original-placeholder')) {
            ta.setAttribute('data-original-placeholder', ta.placeholder);
        }
    });
});
</script>
</div>
