@extends('layouts.master')

@section('title', 'Add New Employee')

@section('css')
<!-- Add any required styles for your form here -->
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Employees
@endslot
@slot('li_2')
{{ route('employees.index') }}
@endslot
@slot('title')
Add New Employee
@endslot
@endcomponent

<!-- Add New Employee Form -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hour_rate.store') }}" method="POST">
                    @csrf
                    <label for="employee_id">Select Employee:</label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    <label for="hour_rate">Hour Rate:</label>
                    <input type="number" name="hour_rate" id="hour_rate" class="form-control" required>

                    <label for="currency">Currency:</label>
                    <input type="text" name="currency" id="currency" class="form-control" required>

                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- Optional JS for form validations if needed -->
@endsection