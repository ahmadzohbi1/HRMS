@extends('layouts.master')

@section('title', 'Employees List')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Employees @endslot
    @slot('li_2') {{ route('employees.index') }} @endslot
    @slot('title') Employees List @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('employees.create') }}"
                            class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>Add Employee
                        </a>
                        <form method="GET" action="{{ route('employees.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search employees..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>


                    <table class="table table-hover table-bordered nowrap w-100" id="employeeTable">
                        <thead>
                            <tr class="table-light">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Timesheet</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->phone }}</td>
                                    <td>{{ $employee->position->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('employees.department.index', $employee->id) }}"
                                            class="btn btn-md btn-secondary">Departments</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('employee.timelogs.index', $employee->id) }}"
                                            class="btn btn-md btn-primary">Timesheet</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('employees.show', $employee->id) }}"
                                            class="btn btn-info btn-md me-2">View</a>
                                        <a href="{{ route('employees.edit', $employee->id) }}"
                                            class="btn btn-warning btn-md">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="custom-pagination d-flex justify-content-center mt-3">
                        @if ($data->lastPage() > 1)
                            <ul class="pagination">
                             @for ($i = 1; $i <= $data->lastPage(); $i++)
                                <li class="page-item {{ ($data->currentPage() == $i) ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $data->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                </li>
                             @endfor
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById("searchButton").addEventListener("click", function () {
                let filter = document.getElementById("searchInput").value.toLowerCase();
                let rows = document.querySelectorAll("#employeeTable tbody tr");
                rows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        </script>
    @endsection
@endsection