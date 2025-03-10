@extends('layouts.master')

@section('title')
    Edit Shift
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Shifts
    @endslot
    @slot('li_2')
    {{ route('shifts.index') }}
    @endslot
    @slot('title')
    Edit Shift
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('shifts.update', $shift->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Shift Type -->
                        <div class="form-group mb-3">
                            <label for="shift_type">Shift Type</label>
                            <select id="shift_type" name="shift_type" class="form-control" required>
                                <option value="company" {{ $shift->shift_type == 'company' ? 'selected' : '' }}>Company
                                </option>
                                <option value="department" {{ $shift->shift_type == 'department' ? 'selected' : '' }}>
                                    Department</option>
                                <option value="employee" {{ $shift->shift_type == 'employee' ? 'selected' : '' }}>Personal
                                </option>

                            </select>
                        </div>

                        <!-- Shift Name -->
                        <div class="form-group mb-3">
                            <label for="shift_name">Shift Name</label>
                            <input type="text" class="form-control" id="shift_name" name="shift_name"
                                value="{{ old('shift_name', $shift->shift_name) }}" required>
                        </div>

                        <!-- Time In -->
                        <div class="form-group mb-3">
                            <label for="time_in">Time In</label>
                            <input type="time" class="form-control" id="time_in" name="time_in"
                                value="{{ old('time_in', \Carbon\Carbon::parse($shift->time_in)->format('H:i')) }}"
                                required>
                        </div>

                        <!-- Time Out -->
                        <div class="form-group mb-3">
                            <label for="time_out">Time Out</label>
                            <input type="time" class="form-control" id="time_out" name="time_out"
                                value="{{ old('time_out', \Carbon\Carbon::parse($shift->time_out)->format('H:i')) }}"
                                required>
                        </div>

                        <!-- Department Dropdown (only for department shifts) -->
                        <div id="department_field" class="form-group mb-3"
                            style="{{ old('shift_type', $shift->shift_type) == 'department' ? 'display: block;' : 'display: none;' }}">

                            <label for="department_id">Department</label>
                            <select id="department_id" name="department_id" class="form-control">
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $shift->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee Dropdown (only for personal shifts) -->
                        <div id="employee_field" class="form-group mb-3"
                            style="{{ old('shift_type', $shift->shift_type) == 'employee' ? 'display: block;' : 'display: none;' }}">

                            <label for="employee_id">Employee</label>
                            <select id="employee_id" name="employee_id" class="form-control">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $shift->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        // Handle dynamic fields based on shift type
        document.addEventListener('DOMContentLoaded', function () {
            var shiftType = document.getElementById('shift_type').value;
            updateFields(shiftType);
        });

        document.getElementById('shift_type').addEventListener('change', function () {
            updateFields(this.value);
        });

        function updateFields(shiftType) {
            var departmentField = document.getElementById('department_field');
            var employeeField = document.getElementById('employee_field');

            departmentField.style.display = 'none';
            employeeField.style.display = 'none';

            if (shiftType === 'department') {
                departmentField.style.display = 'block';
            } else if (shiftType === 'employee') {
                employeeField.style.display = 'block';
            }
        }
        // Trigger the initial event to show the appropriate fields based on the current shift type
        document.getElementById('shift_type').dispatchEvent(new Event('change'));
    </script>
@endsection