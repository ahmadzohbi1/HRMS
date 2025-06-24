@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Vacations @endslot
    @slot('title') Vacations List @endslot
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

                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('vacations.create') }}"
                            class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>
                            Add Vacation
                        </a>
                        &nbsp;
                        <a href="{{ route('vacation-types.index') }}"
                            class="btn btn-rounded btn-primary waves-effect waves-light">
                            <i class="bx font-size-16 me-2 align-middle"></i>
                            Vacation Types
                        </a>
                        &nbsp;
                        <a href="{{ route('vacation-balances.index') }}"
                            class="btn btn-rounded btn-warning waves-effect waves-light">
                            <i class="bx font-size-16 me-2 align-middle"></i>
                            Vacation Balances
                        </a>
                    </div>


                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Vacation Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vacations as $vacation)
                                <tr>
                                    <td>{{ $vacation->id }}</td>
                                    <td>{{ $vacation->employee->name }}</td>
                                    <td>{{ $vacation->vacationType->name }}</td>
                                    <td>{{ $vacation->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $vacation->end_date->format('Y-m-d') }}</td>
                                    <td>{{ $vacation->duration_in_days }} days</td>
                                    <td>
                                        <span
                                            class="badge badge-soft-{{ $vacation->status == 'approved' ? 'success' : ($vacation->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($vacation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vacations.show', $vacation->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-show"></i> View
                                        </a>
                                        <a href="{{ route('vacations.edit', $vacation->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('vacations.destroy', $vacation->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this vacation request?')">
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