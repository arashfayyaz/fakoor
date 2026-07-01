<div class="cta-buttons">
    <ul>

        <li>
            <a href="{{ route('contact') }}" class="cta-link cta-link--ghost">
                تماس با ما
            </a>
        </li>

        <li>
            <a href="{{route('courses')}}" class="cta-link cta-link--ghost">دوره های آموزشی </a>
        </li>
        <li>
            <a href="{{route('exams')}}" class="cta-link cta-link--ghost">آزمون ها </a>
        </li>
        <li>
            <a href="{{route('articles', \App\Enums\ArticleEnum::ARTICLES)}}" class="cta-link cta-link--ghost">مقالات
            </a>
        </li>
        {{-- <li>--}}
            {{-- <a href="{{route('articles',\App\Enums\ArticleEnum::NEWS)}}">اخبار </a>--}}
            {{-- </li>--}}
    </ul>
</div>
