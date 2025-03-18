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
                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('vacations.create') }}" class="btn btn-rounded btn-success waves-effect waves-light">
                        <i class="bx bx-plus font-size-16 me-2 align-middle"></i>    
                        Add Vacation</a>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Vacation Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
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
                                    <td>{{ $vacation->start_date }}</td>
                                    <td>{{ $vacation->end_date }}</td>
                                    <td>{{ ucfirst($vacation->status) }}</td>
                                    <td>
                                        <a href="{{ route('vacations.show', $vacation->id) }}" class="btn btn-primary btn-md">View</a>
                                        <a href="{{ route('vacations.edit', $vacation->id) }}" class="btn btn-warning btn-md">Edit</a>
                                        <form action="{{ route('vacations.destroy', $vacation->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-md" onclick="return confirm('Are you sure you want to delete this vacation request?')">Delete</button>
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