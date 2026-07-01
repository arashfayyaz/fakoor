<div>
    @section('title','پکیج های آزمون')
    <x-admin.form-control link="{{ route('admin.store.quiz-package',['create'] ) }}" title="پکیج های آزمون"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            @include('admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped table-bordered" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>نام مستعار</th>
                            <th>وضعیت</th>
                            <th>تعداد آزمون</th>
                            <th>قیمت</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($packages as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>{{ $item->status_label }}</td>
                                <td>{{ $item->quizzes_count }}</td>
                                <td>{{ number_format($item->price) }} تومان</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.quiz-package',['edit', $item->id]) }}" />
                                    <x-admin.delete-btn onclick="deleteItem({{$item->id}})" />
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="7">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$packages->links('admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
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
                    @this.call('delete', id)
                }
            })
        }
    </script>
@endpush
