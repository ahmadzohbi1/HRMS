@extends('layouts.master')

@section('title')
Roles
@endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Roles
@endslot
@slot('li_2')
{{ route('roles.index') }}
@endslot
@slot('title')
Roles List
@endslot
@endcomponent

{{-- import modal --}}
@component('components.file-import')
@slot('route')
{{ route('roles.index') }}
@endslot
@endcomponent

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-end mb-4" id="action_btns">
          <a href="{{ route('roles.create') }}" class="btn btn-rounded btn-success waves-effect waves-light">
            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>Add New Role
          </a>
        </div>
        <div class="table-responsive">
          <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Role Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($roles as $role)
          <tr>
          <td>{{ $role->id }}</td>
          <td>{{ $role->name }}</td>
          <td>
            <!-- Hide actions for Super Admin role -->
            @if ($role->name !== 'Super Admin')
        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary">
        Edit
        </a>
        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
          Delete
        </button>
        </form>
      @else
    <span class="text-muted">No Actions Available</span>
  @endif
          </td>
          </tr>
        @endforeach
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div> <!-- end col -->
</div> <!-- end row -->
@endsection

@section('script')
<!-- Required datatable js -->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>

{{-- DataTable initialization --}}
<script>
  $(document).ready(function () {
    $('#datatable').DataTable();
  });
</script>
@endsection