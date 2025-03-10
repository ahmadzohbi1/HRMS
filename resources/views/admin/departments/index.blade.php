@extends('layouts.master')

@section('title')
Departments List
@endsection

@section('css')
<!-- DataTables -->
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Departments
@endslot
@slot('li_2')

@endslot
@slot('title')
Departments List
@endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-4">
                    <a href="{{route('departments.create')}}"
                        class="btn btn-rounded btn-success waves-effect waves-light">
                        <i class="bx bx-plus font-size-16 me-2 align-middle"></i>Add Department
                    </a>
                </div>
                <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Department Name</th>
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
            order: [[0, "desc"]],
            ajax: "{{ route('departments.index') }}",
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'created_at', render: function (data) { return new Date(data).toLocaleDateString(); } },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
