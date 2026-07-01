@php
    $isActive = in_array($transcript->result, [\App\Enums\QuizEnum::PENDING, \App\Enums\QuizEnum::SUSPENDED]);
    $isSuspended = $transcript->result == \App\Enums\QuizEnum::SUSPENDED;
    $startLabel = $isSuspended ? 'ادامه آزمون' : 'شروع آزمون';
    $score = $transcript->score;
    $totalScore = (float) ($transcript->quiz->total_score ?: 0);
    $scorePercent = !is_null($score) && $totalScore > 0 ? min(100, max(0, round(((float) $score / $totalScore) * 100))) : 0;
    $answersScore = $transcript->answers->map(fn ($item) => $item->question_score)->sum();
    $statusClass = match ($transcript->result) {
        \App\Enums\QuizEnum::PASSED => 'is-passed',
        \App\Enums\QuizEnum::REJECTED => 'is-rejected',
        \App\Enums\QuizEnum::SUSPENDED => 'is-running',
        default => 'is-pending',
    };
@endphp

<div class="exam-overview-page">
    <div class="dashboard-menu-toggler btn theme-btn theme-btn-sm lh-28 theme-btn-transparent mb-4 ml-3">
        <i class="la la-bars mr-1"></i> منو
    </div>

    <div class="container-fluid">
        <div class="exam-overview-hero">
            <div class="exam-overview-hero__content">
                <span class="exam-status-badge {{ $statusClass }}">{{ $transcript->result_label }}</span>
                <h3>{{ $transcript->quiz->name }}</h3>
                <p>{{ $transcript->course_data['title'] ?? 'آزمون مستقل' }}</p>
            </div>

            <div class="exam-overview-hero__action">
                @if($isActive && $userDetails)
                    <button class="btn exam-primary-btn" onclick="enter_quiz()">
                        <i class="la la-door-open"></i>
                        <span>{{ $startLabel }}</span>
                    </button>
                @elseif($isActive)
                    <a class="btn exam-primary-btn" href="{{ route('user.profile') }}">
                        <i class="la la-user-check"></i>
                        <span>تکمیل پروفایل</span>
                    </a>
                @elseif($nextTranscript)
                    <a class="btn exam-primary-btn" href="{{ route('user.quiz', $nextTranscript->id) }}">
                        <i class="la la-redo-alt"></i>
                        <span>آزمون مجدد</span>
                    </a>
                @endif
            </div>
        </div>

        @if(!$userDetails)
            <div class="exam-inline-alert">
                <i class="la la-exclamation-circle"></i>
                <div>
                    <strong>تکمیل پروفایل لازم است.</strong>
                    <span>برای شروع آزمون، اطلاعات پروفایل خود را کامل کنید.</span>
                </div>
                <a href="{{ route('user.profile') }}">تکمیل پروفایل</a>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="exam-panel">
                    <div class="exam-panel__header">
                        <div>
                            <h4>نمای کلی آزمون</h4>
                            <span>شماره کارنامه: {{ $transcript->id }}</span>
                        </div>
                        <i class="la la-clipboard-check"></i>
                    </div>

                    <div class="exam-stat-grid">
                        <div class="exam-stat-card">
                            <span>حداقل نمره قبولی</span>
                            <strong>{{ $transcript->quiz->minimum_score }}</strong>
                        </div>
                        <div class="exam-stat-card">
                            <span>بارم کل</span>
                            <strong>{{ $transcript->quiz->total_score }}</strong>
                        </div>
                        <div class="exam-stat-card">
                            <span>نمره شما</span>
                            <strong>{{ $score ?? '-' }}</strong>
                        </div>
                    </div>

                    @if(! $isActive)
                        <div class="exam-score-card">
                            <div class="exam-score-card__meta">
                                <span>نتیجه نهایی</span>
                                <strong>{{ $transcript->result_label }}</strong>
                            </div>
                            <div class="exam-score-card__bar" aria-hidden="true">
                                <span style="width: {{ $scorePercent }}%"></span>
                            </div>
                            <div class="exam-score-card__numbers">
                                <span>{{ $score ?? 0 }} از {{ $transcript->quiz->total_score }}</span>
                                <span>{{ $scorePercent }}%</span>
                            </div>
                        </div>

                        <div class="exam-report-list">
                            <div>
                                <span>مجموع نمرات سؤال‌ها</span>
                                <strong>{{ $answersScore }}</strong>
                            </div>
                            <div>
                                <span>محصول</span>
                                <strong>{{ $transcript->course_data['title'] ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>تاریخ</span>
                                <strong>{{ $transcript->date }}</strong>
                            </div>
                            @if($transcript->certificate)
                                <div>
                                    <span>گواهینامه</span>
                                    <strong>
                                        <a href="{{ route('user.certificate', $transcript->certificate->id) }}">
                                            {{ $transcript->certificate->certificate->name }}
                                        </a>
                                    </strong>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="exam-ready-card">
                        <div class="exam-ready-card__icon">
                                <i class="la la-hourglass-start"></i>
                            </div>
                            <div>
                                <h5>{{ $isSuspended ? 'آزمون شما در جریان است.' : 'آزمون آماده شروع است.' }}</h5>
                                <p>{{ $transcript->quiz->time }} دقیقه، {{ $transcript->quiz->question_count }} سؤال</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="exam-panel exam-side-panel">
                    <div class="exam-panel__header">
                        <div>
                            <h4>جزئیات آزمون</h4>
                            <span>{{ $transcript->quiz->question_count }} سؤال</span>
                        </div>
                        <i class="la la-info-circle"></i>
                    </div>

                    <div class="exam-detail-list">
                        <div>
                            <i class="la la-certificate"></i>
                            <span>گواهینامه</span>
                            <strong>{{ $transcript->quiz->certificate->title ?? 'ندارد' }}</strong>
                        </div>
                        <div>
                            <i class="la la-clock"></i>
                            <span>زمان</span>
                            <strong>{{ $transcript->quiz->time }} دقیقه</strong>
                        </div>
                        <div>
                            <i class="la la-question-circle"></i>
                            <span>تعداد سؤال</span>
                            <strong>{{ $transcript->quiz->question_count }}</strong>
                        </div>
                        <div>
                            <i class="la la-box"></i>
                            <span>محصول</span>
                            <strong>{{ $transcript->course_data['title'] ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head')
    <style>
        .exam-overview-page {
            --exam-navy: #16213e;
            --exam-teal: #0f766e;
            --exam-orange: #f97316;
            --exam-red: #dc2626;
            --exam-border: #e6eaf0;
            --exam-muted: #6b7280;
            background: #f7f9fc;
            min-height: calc(100vh - 90px);
            padding-bottom: 36px;
        }

        .exam-overview-hero,
        .exam-panel,
        .exam-inline-alert {
            border: 1px solid var(--exam-border);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 12px 34px rgba(22, 33, 62, 0.07);
        }

        .exam-overview-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
            padding: 24px;
            border-right: 5px solid var(--exam-teal);
        }

        .exam-overview-hero__content h3 {
            color: var(--exam-navy);
            font-size: 26px;
            font-weight: 800;
            line-height: 1.7;
            margin: 8px 0 4px;
        }

        .exam-overview-hero__content p,
        .exam-panel__header span,
        .exam-stat-card span,
        .exam-score-card__meta span,
        .exam-report-list span,
        .exam-detail-list span,
        .exam-ready-card p {
            color: var(--exam-muted);
        }

        .exam-status-badge {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            border-radius: 6px;
            padding: 5px 12px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .exam-status-badge.is-passed { background: var(--exam-teal); }
        .exam-status-badge.is-rejected { background: var(--exam-red); }
        .exam-status-badge.is-running { background: var(--exam-orange); }
        .exam-status-badge.is-pending { background: var(--exam-navy); }

        .exam-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 160px;
            min-height: 46px;
            border-radius: 8px;
            background: var(--exam-teal);
            color: #fff;
            font-weight: 800;
            box-shadow: 0 10px 22px rgba(15, 118, 110, 0.24);
        }

        .exam-primary-btn:hover {
            color: #fff;
            background: #0b655f;
        }

        .exam-inline-alert {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            padding: 14px 16px;
            border-right: 5px solid var(--exam-orange);
        }

        .exam-inline-alert i {
            color: var(--exam-orange);
            font-size: 28px;
        }

        .exam-inline-alert div {
            flex: 1;
        }

        .exam-inline-alert strong,
        .exam-inline-alert span {
            display: block;
            line-height: 1.7;
        }

        .exam-inline-alert a {
            color: var(--exam-teal);
            font-weight: 800;
            white-space: nowrap;
        }

        .exam-panel {
            margin-bottom: 20px;
            padding: 22px;
        }

        .exam-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .exam-panel__header h4 {
            color: var(--exam-navy);
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .exam-panel__header > i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: #eef7f6;
            color: var(--exam-teal);
            font-size: 26px;
        }

        .exam-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .exam-stat-card {
            min-height: 104px;
            border: 1px solid var(--exam-border);
            border-radius: 8px;
            padding: 16px;
            background: #fbfcfe;
        }

        .exam-stat-card span {
            display: block;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .exam-stat-card strong {
            color: var(--exam-navy);
            font-size: 28px;
            font-weight: 900;
        }

        .exam-ready-card,
        .exam-score-card,
        .exam-report-list {
            margin-top: 16px;
        }

        .exam-ready-card {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            border-radius: 8px;
            background: #f1f8f7;
            padding: 16px;
        }

        .exam-ready-card__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            border-radius: 8px;
            background: #fff;
            color: var(--exam-teal);
            font-size: 28px;
        }

        .exam-ready-card h5 {
            color: var(--exam-navy);
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .exam-score-card {
            border-radius: 8px;
            border: 1px solid var(--exam-border);
            padding: 16px;
        }

        .exam-score-card__meta,
        .exam-score-card__numbers {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .exam-score-card__meta strong {
            color: var(--exam-navy);
            font-weight: 900;
        }

        .exam-score-card__bar {
            height: 10px;
            margin: 14px 0;
            border-radius: 999px;
            background: #e8edf3;
            overflow: hidden;
        }

        .exam-score-card__bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--exam-teal);
        }

        .exam-score-card__numbers {
            color: var(--exam-muted);
            font-size: 13px;
            font-weight: 700;
        }

        .exam-report-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .exam-report-list div,
        .exam-detail-list div {
            border: 1px solid var(--exam-border);
            border-radius: 8px;
            background: #fff;
            padding: 14px;
        }

        .exam-report-list span,
        .exam-report-list strong {
            display: block;
            line-height: 1.7;
        }

        .exam-report-list strong {
            color: var(--exam-navy);
            font-weight: 800;
            word-break: break-word;
        }

        .exam-detail-list {
            display: grid;
            gap: 12px;
        }

        .exam-detail-list div {
            display: grid;
            grid-template-columns: 34px 1fr auto;
            align-items: center;
            gap: 10px;
        }

        .exam-detail-list i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #fff4ea;
            color: var(--exam-orange);
            font-size: 22px;
        }

        .exam-detail-list strong {
            color: var(--exam-navy);
            font-weight: 800;
            text-align: left;
            word-break: break-word;
        }

        @media (max-width: 991px) {
            .exam-overview-hero {
                align-items: flex-start;
                flex-direction: column;
            }

            .exam-overview-hero__action,
            .exam-primary-btn {
                width: 100%;
            }
        }

        @media (max-width: 767px) {
            .exam-overview-hero,
            .exam-panel {
                padding: 18px;
            }

            .exam-overview-hero__content h3 {
                font-size: 22px;
            }

            .exam-stat-grid,
            .exam-report-list {
                grid-template-columns: 1fr;
            }

            .exam-inline-alert {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .exam-inline-alert a {
                width: 100%;
            }

            .exam-detail-list div {
                grid-template-columns: 34px 1fr;
            }

            .exam-detail-list strong {
                grid-column: 2;
                text-align: right;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function enter_quiz() {
            Swal.fire({
                title: 'ورود به آزمون',
                text: 'از شروع یا ادامه این آزمون اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#dc2626',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('enter_quiz')
                }
            })
        }
    </script>
@endpush
