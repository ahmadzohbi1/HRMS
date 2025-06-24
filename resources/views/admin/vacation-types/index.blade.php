@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Types @endslot
        @slot('title') Vacation Types List @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('vacation-types.create') }}" class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>    
                            Add Vacation Type
                        </a>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Max Days</th>
                                <th>Total Vacations</th>
                                <th>Total Balances</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vacationTypes as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td>{{ $type->name }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $type->is_paid ? 'success' : 'secondary' }}">
                                            {{ $type->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $type->isUnlimited() ? 'Unlimited' : $type->max_days_allowed . ' days' }}
                                    </td>
                                    <td>{{ $type->vacations_count }}</td>
                                    <td>{{ $type->employee_vacation_balances_count }}</td>
                                    <td>
                                        <a href="{{ route('vacation-types.show', $type->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-show"></i> View
                                        </a>
                                        <a href="{{ route('vacation-types.edit', $type->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('vacation-types.destroy', $type->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this vacation type?')">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection