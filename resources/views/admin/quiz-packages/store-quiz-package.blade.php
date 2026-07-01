<div>
    @section('title','پکیج آزمون')
    <x-admin.form-control deleteAble="true" deleteContent="حذف پکیج آزمون" mode="{{$mode}}" title="پکیج آزمون"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <div class="row">
                <x-admin.forms.input with="6" type="text" id="title" label="عنوان*" wire:model.defer="title"/>
                <x-admin.forms.input with="6" type="text" id="slug" label="نام مستعار*" wire:model.defer="slug"/>
                <x-admin.forms.input with="6" type="number" id="const_price" label="قیمت" wire:model.defer="const_price"/>
                <x-admin.forms.input with="6" type="number" id="enter_count" label="تعداد دفعات مجاز شرکت*" wire:model.defer="enter_count"/>
                <x-admin.forms.dropdown with="6" id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
                <x-admin.forms.dropdown with="6" id="reduction_type" :data="$data['reduction']" label="نوع تخفیف" wire:model.defer="reduction_type"/>
                <x-admin.forms.input with="6" type="number" min="0" id="reduction_value" label="مقدار تخفیف*" wire:model.defer="reduction_value"/>
                <x-admin.forms.jdate-picker with="6" id="start_at" label="شروع تخفیف" wire:model.defer="start_at"/>
                <x-admin.forms.jdate-picker with="6" id="expire_at" label="پایان تخفیف" wire:model.defer="expire_at"/>
            </div>
            <hr>
            <x-admin.forms.checkbox value="1" id="sellable" label="قابل خرید" wire:model.defer="sellable" />
            <x-admin.forms.full-text-editor id="descriptions" label="توضیحات" wire:model.defer="descriptions"/>
            <x-admin.forms.lfm-standalone id="image" label="تصویر*" :file="$image" type="image" required="true" wire:model="image"/>
            <x-admin.forms.text-area label="کلمات کلیدی" help="کلمات را با کاما از هم جدا کنید" wire:model.defer="seo_keywords" id="seo_keywords" />
            <x-admin.forms.text-area label="توضیحات سئو" wire:model.defer="seo_description" id="seo_description" />

            <x-admin.form-section label="آزمون های داخل پکیج">
                <div class="row">
                    @foreach($data['quizzes'] as $item)
                        @php
                            $quizId = data_get($item, 'id');
                            $quizName = data_get($item, 'name');
                        @endphp
                        <div class="col-lg-4 col-md-6 col-12">
                            <x-admin.forms.checkbox value="{{$quizId}}" id="quiz_package_{{$quizId}}" label="{{$quizName}}" wire:model.defer="quizzes.{{$quizId}}" />
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem() {
            Swal.fire({
                title: 'حذف پکیج آزمون!',
                text: 'آیا از حذف این پکیج اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                    @this.call('deleteItem')
                }
            })
        }
    </script>
@endpush
