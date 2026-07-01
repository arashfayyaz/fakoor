@php
    $currentQuestion = (int) ($page ?? 1);
    $totalQuestions = max((int) $question_count, 1);
    $progressPercent = min(100, max(0, round(($currentQuestion / $totalQuestions) * 100)));
    $answeredCount = collect($answers)->filter(fn ($value) => !empty($value))->count();
    $remainingCount = max($totalQuestions - $answeredCount, 0);
@endphp

<div wire:init="setTimer()" class="live-exam-page">
    <section class="live-exam-shell">
        <div class="container">
            <div class="live-exam-topbar">
                <div class="live-exam-title">
                    <span>آزمون</span>
                    <h2>{{ $transcript->quiz->name }}</h2>
                </div>

                <div class="live-exam-timer" wire:ignore>
                    <i class="la la-clock"></i>
                    <div>
                        <span>زمان باقی‌مانده</span>
                        <strong id="clock">--:--:--</strong>
                    </div>
                </div>

                <button onclick="finish()" wire:loading.attr="disabled" class="btn live-exam-finish-btn">
                    <i class="la la-flag-checkered"></i>
                    <span>پایان آزمون</span>
                </button>
            </div>

            <div class="live-exam-progress">
                <div class="live-exam-progress__meta">
                    <span>سؤال {{ $currentQuestion }} از {{ $totalQuestions }}</span>
                    <strong>{{ $progressPercent }}%</strong>
                </div>
                <div class="live-exam-progress__bar" aria-hidden="true">
                    <span style="width: {{ $progressPercent }}%"></span>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 col-lg-8">
                    @foreach($question as $item)
                        <article class="live-exam-question-card">
                            <div class="live-exam-question-card__head">
                                <div>
                                    <span class="live-exam-pill">سؤال {{ $currentQuestion }}</span>
                                    <h3>متن سؤال</h3>
                                </div>
                                <span class="live-exam-difficulty">{{ $item->difficulty_label }}</span>
                            </div>

                            <div class="live-exam-question-text">
                                {!! $item->text !!}
                            </div>
                        </article>

                        <section class="live-exam-answer-card">
                            <div class="live-exam-answer-card__head">
                                <div>
                                    <h3>گزینه‌ها</h3>
                                    <span>{{ ! empty($answers[$item->id]) ? 'پاسخ انتخاب شده' : 'بدون پاسخ' }}</span>
                                </div>
                                <button wire:loading.attr="disabled" wire:click="undo({{ $item->id }})" class="btn live-exam-clear-btn">
                                    <i class="la la-times-circle"></i>
                                    <span>پاک کردن پاسخ</span>
                                </button>
                            </div>

                            <div class="row live-exam-choice-row">
                                @foreach($item->choices as $choice)
                                    <div class="{{ $transcript->quiz->show_choices_type == App\Enums\QuizEnum::SHOW_SIDE_BY_SIDE ? 'col-md-6 col-xl-6' : 'col-12' }}">
                                        <div class="live-exam-choice">
                                            <input
                                                wire:model="answers.{{ $item->id }}"
                                                type="radio"
                                                id="choice-{{ $choice->id }}"
                                                name="question-{{ $item->id }}"
                                                value="{{ $choice->id }}"
                                                class="live-exam-choice-input"
                                            />
                                            <label class="live-exam-choice-card" for="choice-{{ $choice->id }}">
                                                <span class="live-exam-choice-marker">{{ $loop->iteration }}</span>
                                                <span class="live-exam-choice-title">{{ $choice->title }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="live-exam-saving" wire:loading wire:target="answers.{{ $item->id }},undo({{ $item->id }})">
                                <i class="la la-sync"></i>
                                <span>در حال ذخیره...</span>
                            </div>
                        </section>
                    @endforeach

                    <div class="live-exam-pagination">
                        {{ $question->links('site.includes.paginate-exam') }}
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4">
                    <aside class="live-exam-side">
                        <div class="live-exam-side__head">
                            <i class="la la-chart-pie"></i>
                            <div>
                                <h3>وضعیت آزمون</h3>
                                <span>{{ $answeredCount }} پاسخ ثبت‌شده</span>
                            </div>
                        </div>

                        <div class="live-exam-side-stat">
                            <span>پاسخ داده‌شده</span>
                            <strong>{{ $answeredCount }}</strong>
                        </div>
                        <div class="live-exam-side-stat">
                            <span>باقی‌مانده</span>
                            <strong>{{ $remainingCount }}</strong>
                        </div>
                        <div class="live-exam-side-stat">
                            <span>کل سؤال‌ها</span>
                            <strong>{{ $totalQuestions }}</strong>
                        </div>

                        <div class="live-exam-side-note">
                            <i class="la la-shield-alt"></i>
                            <span>آزمون در حال برگزاری</span>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</div>

@push('head')
    <style>
        .live-exam-page {
            --exam-navy: #16213e;
            --exam-teal: #0f766e;
            --exam-orange: #f97316;
            --exam-red: #dc2626;
            --exam-border: #e6eaf0;
            --exam-muted: #6b7280;
            background: #f6f8fb;
            min-height: 100vh;
        }

        .live-exam-shell {
            padding: 28px 0 52px;
        }

        .live-exam-topbar,
        .live-exam-progress,
        .live-exam-question-card,
        .live-exam-answer-card,
        .live-exam-side {
            border: 1px solid var(--exam-border);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 12px 34px rgba(22, 33, 62, 0.07);
        }

        .live-exam-topbar {
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
            gap: 16px;
            padding: 18px;
            border-right: 5px solid var(--exam-teal);
        }

        .live-exam-title span,
        .live-exam-timer span,
        .live-exam-progress__meta span,
        .live-exam-answer-card__head span,
        .live-exam-side__head span,
        .live-exam-side-stat span,
        .live-exam-side-note span {
            color: var(--exam-muted);
        }

        .live-exam-title h2 {
            color: var(--exam-navy);
            font-size: 23px;
            font-weight: 900;
            line-height: 1.7;
            margin: 2px 0 0;
        }

        .live-exam-timer {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 210px;
            min-height: 56px;
            border: 1px solid #fde7d4;
            border-radius: 8px;
            background: #fff8f1;
            padding: 10px 14px;
        }

        .live-exam-timer i {
            color: var(--exam-orange);
            font-size: 28px;
        }

        .live-exam-timer strong {
            display: block;
            color: var(--exam-navy);
            font-size: 20px;
            font-weight: 900;
            direction: ltr;
            text-align: right;
        }

        .live-exam-finish-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            border-radius: 8px;
            background: var(--exam-red);
            color: #fff;
            font-weight: 800;
            white-space: nowrap;
        }

        .live-exam-finish-btn:hover {
            color: #fff;
            background: #b91c1c;
        }

        .live-exam-progress {
            margin: 16px 0;
            padding: 16px;
        }

        .live-exam-progress__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .live-exam-progress__meta strong {
            color: var(--exam-teal);
        }

        .live-exam-progress__bar {
            height: 10px;
            border-radius: 999px;
            background: #e8edf3;
            overflow: hidden;
        }

        .live-exam-progress__bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--exam-teal);
        }

        .live-exam-question-card,
        .live-exam-answer-card,
        .live-exam-side {
            padding: 22px;
            margin-bottom: 16px;
        }

        .live-exam-question-card__head,
        .live-exam-answer-card__head,
        .live-exam-side__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }

        .live-exam-question-card__head h3,
        .live-exam-answer-card__head h3,
        .live-exam-side__head h3 {
            color: var(--exam-navy);
            font-size: 19px;
            font-weight: 900;
            margin: 5px 0 0;
        }

        .live-exam-pill,
        .live-exam-difficulty {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 13px;
            font-weight: 800;
        }

        .live-exam-pill {
            background: #eef7f6;
            color: var(--exam-teal);
        }

        .live-exam-difficulty {
            background: #fff4ea;
            color: var(--exam-orange);
            white-space: nowrap;
        }

        .live-exam-question-text {
            color: #253047;
            font-size: 17px;
            line-height: 2.1;
            word-break: break-word;
        }

        .live-exam-question-text p:last-child {
            margin-bottom: 0;
        }

        .live-exam-clear-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 38px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fff5f5;
            color: var(--exam-red);
            font-weight: 800;
        }

        .live-exam-clear-btn:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .live-exam-choice-row {
            margin-right: -6px;
            margin-left: -6px;
        }

        .live-exam-choice-row > [class*="col-"] {
            padding-right: 6px;
            padding-left: 6px;
        }

        .live-exam-choice {
            position: relative;
            margin-bottom: 12px;
        }

        .live-exam-choice-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .live-exam-choice-card {
            display: grid;
            grid-template-columns: 34px 1fr;
            align-items: center;
            gap: 12px;
            min-height: 64px;
            width: 100%;
            margin: 0;
            border: 1px solid var(--exam-border);
            border-radius: 8px;
            background: #fbfcfe;
            padding: 12px 14px;
            color: #253047;
            cursor: pointer;
            transition: border-color 0.16s ease, background 0.16s ease, box-shadow 0.16s ease;
        }

        .live-exam-choice-card:hover {
            border-color: #99d5cf;
            background: #f4fbfa;
        }

        .live-exam-choice-input:focus + .live-exam-choice-card {
            border-color: var(--exam-teal);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        }

        .live-exam-choice-input:checked + .live-exam-choice-card {
            border-color: var(--exam-teal);
            background: #eef7f6;
            box-shadow: inset 4px 0 0 var(--exam-teal);
        }

        .live-exam-choice-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e8edf3;
            color: var(--exam-navy);
            font-weight: 900;
        }

        .live-exam-choice-input:checked + .live-exam-choice-card .live-exam-choice-marker {
            background: var(--exam-teal);
            color: #fff;
        }

        .live-exam-choice-title {
            line-height: 1.8;
            word-break: break-word;
        }

        .live-exam-saving {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--exam-teal);
            font-weight: 800;
        }

        .live-exam-saving i {
            font-size: 18px;
        }

        .live-exam-side {
            position: sticky;
            top: 92px;
        }

        .live-exam-side__head {
            justify-content: flex-start;
        }

        .live-exam-side__head > i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: #eef7f6;
            color: var(--exam-teal);
            font-size: 28px;
        }

        .live-exam-side-stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 52px;
            border: 1px solid var(--exam-border);
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .live-exam-side-stat strong {
            color: var(--exam-navy);
            font-size: 22px;
            font-weight: 900;
        }

        .live-exam-side-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-top: 14px;
            border-radius: 8px;
            background: #fff8f1;
            padding: 12px;
            line-height: 1.8;
        }

        .live-exam-side-note i {
            color: var(--exam-orange);
            font-size: 22px;
            margin-top: 2px;
        }

        .live-exam-pagination .pagination {
            flex-wrap: wrap;
            gap: 6px;
        }

        .live-exam-pagination .page-link {
            min-height: 40px;
            border-radius: 8px;
            border-color: var(--exam-border);
            color: var(--exam-navy);
            font-weight: 800;
        }

        .live-exam-pagination .page-item.active .page-link {
            background: var(--exam-teal);
            border-color: var(--exam-teal);
        }

        @media (max-width: 991px) {
            .live-exam-topbar {
                grid-template-columns: 1fr;
            }

            .live-exam-timer,
            .live-exam-finish-btn {
                width: 100%;
            }

            .live-exam-side {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .live-exam-shell {
                padding-top: 18px;
            }

            .live-exam-topbar,
            .live-exam-progress,
            .live-exam-question-card,
            .live-exam-answer-card,
            .live-exam-side {
                padding: 16px;
            }

            .live-exam-title h2 {
                font-size: 20px;
            }

            .live-exam-question-card__head,
            .live-exam-answer-card__head {
                align-items: flex-start;
                flex-direction: column;
            }

            .live-exam-clear-btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function finish() {
            Swal.fire({
                title: 'اتمام آزمون',
                text: 'از ثبت نهایی پاسخ‌ها اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#dc2626',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('finish')
                }
            })
        }

        Livewire.on('timer', function (data) {
            $('#clock').countdown(data.data)
                .on('update.countdown', function(event) {
                    var format = '%H:%M:%S';
                    if(event.offset.totalDays > 0) {
                        format = '%-d روز ' + format;
                    }
                    if(event.offset.weeks > 0) {
                        format = '%-w هفته ' + format;
                    }
                    $(this).html(event.strftime(format));
                })
                .on('finish.countdown', function(event) {
                    $(this).html('اتمام زمان!')
                        .parent().addClass('disabled');
                    @this.call('finish')
                });
        })
    </script>
@endpush
