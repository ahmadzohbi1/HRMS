@extends('layouts.master')

@section('title')
    Edit Shift
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap CSS (ensure it's included) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Shifts @endslot
        @slot('li_2') {{ route('shifts.index') }} @endslot
        @slot('title') Edit Shift @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('shifts.update', $shift->id) }}">
                        @csrf
                        @method('PUT')

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
                                value="{{ old('time_in', $shift->time_in) }}" required>
                        </div>

                        <!-- Time Out -->
                        <div class="form-group mb-3">
                            <label for="time_out">Time Out</label>
                            <input type="time" class="form-control" id="time_out" name="time_out"
                                value="{{ old('time_out', $shift->time_out) }}" required>
                        </div>

                        <!-- Shift Type Dropdown -->
                        <div class="form-group mb-3">
                            <label for="shift_type">Shift Type</label>
                            <select id="shift_type" name="shift_type" class="form-control" required>
                                <option value="company" {{ old('shift_type', $shift->shift_type) == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="department" {{ old('shift_type', $shift->shift_type) == 'department' ? 'selected' : '' }}>Department</option>
                                <option value="employee" {{ old('shift_type', $shift->shift_type) == 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>

                        <!-- Department Dropdown (only for department shifts) -->
                        <div id="department_field" class="form-group mb-3" style="{{ $shift->shift_type == 'department' ? 'display:block;' : 'display:none;' }}">
                            <label for="department_id">Department</label>
                            <select id="department_id" name="department_id" class="form-control">
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $shift->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee Dropdown (only for employee shifts) -->
                        <div id="employee_field" class="form-group mb-3" style="{{ $shift->shift_type == 'employee' ? 'display:block;' : 'display:none;' }}">
                            <label for="employee_id">Employee</label>
                            <select id="employee_id" name="employee_id" class="form-control">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $shift->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Shift Hidden Field (only for company shifts) -->
                        <div id="company_field" class="form-group mb-3" style="{{ $shift->shift_type == 'company' ? 'display:block;' : 'display:none;' }}">
                            <input type="hidden" name="company_shift" value="1"> <!-- Modify value as needed -->
                        </div>

                        <!-- Shift Rules Selection -->
                        <div class="form-group mb-3">
                            <label for="shift_rules">Select Shift Rules</label><br>
                            @foreach($shiftRules as $rule)
    
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="rule_{{ $rule->id }}" 
                                           name="shift_rules[]" value="{{ $rule->id }}" 
                                           {{ in_array($rule->id, $shift->shiftRules->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rule_{{ $rule->id }}">
                                        {{ $rule->shift_title }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-success">Update Shift</button>
                        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- jQuery (ensure it's loaded before Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle (includes Popper for Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Your custom script to handle dynamic fields -->
    <script>
        // Handle dynamic fields based on shift type
        document.getElementById('shift_type').addEventListener('change', function () {
            var shiftType = this.value;

            // Hide all fields initially
            document.getElementById('department_field').style.display = 'none';
            document.getElementById('employee_field').style.display = 'none';
            document.getElementById('company_field').style.display = 'none';

            // Show the appropriate fields based on the selected shift type
            if (shiftType === 'department') {
                document.getElementById('department_field').style.display = 'block';
            } else if (shiftType === 'employee') {
                document.getElementById('employee_field').style.display = 'block';
            } else if (shiftType === 'company') {
                document.getElementById('company_field').style.display = 'block';
            }
        });

        // Trigger the initial event to set the default visibility of fields
        document.getElementById('shift_type').dispatchEvent(new Event('change'));
    </script>
@endsection
