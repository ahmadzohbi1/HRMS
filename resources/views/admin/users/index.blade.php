@extends('layouts.master')

@section('title')
Admins List
@endsection

@section('css')
<!-- DataTables -->
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Admins
@endslot
@slot('li_2')
{{ route('admins.index') }}
@endslot
@slot('title')
Admins List
@endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-4" id="action_btns">
                    <div class="btn-group mx-3">
                        <!-- <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">@lang('translation.actions') <i
                                class="mdi mdi-chevron-down"></i></button> -->
                    </div>
                    <a href="{{ route('admins.create') }} "
                        class="btn btn-rounded btn-success waves-effect waves-light"><i
                            class="bx bx-plus font-size-16 me-2 align-middle"></i>Add User
                    </a>
                </div>
                <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Ban</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection

@section('script')
<script src="{{ asset('/assets/libs/datatables/datatables.min.js') }}"></script>

<script type="text/javascript">
    $(function () {
        let table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: true,
            lengthMenu: [10, 20, 50, 100],
            pageLength: 10,
            scrollX: true,
            order: [
                [0, "desc"]
            ],
            ajax: "{{ route('admins.index') }}", // Adjust route as needed
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'role' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'action', orderable: false, searchable: false }
            ]

        });

        $(document).on('change', '.ban-toggle', function () {
            let userId = $(this).data('id');
            let isBanned = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admins.toggle-ban') }}", // Route for handling ban toggle
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: userId,
                    ban: isBanned
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: isBanned ? 'User banned successfully.' : 'User unbanned successfully.',
                            timer: 1500,
                        });
                        $('#datatable').DataTable().ajax.reload(null, false); // Reload DataTable without refreshing the page
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `Error: ${xhr.responseText || 'Unknown error occurred.'}`,
                    });
                    console.error('Error details:', { status, error, response: xhr.responseText });
                }
            });
        });
    });
</script>

@endsection