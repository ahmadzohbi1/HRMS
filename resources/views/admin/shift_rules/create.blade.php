@extends('layouts.master')

@section('title')
    Create Shift Rule
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Shifts @endslot
    @slot('li_2') {{ route('shifts.index') }} @endslot
    @slot('title') Create Shift @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('shifts-rules.store') }}">
                        @csrf

                        <!-- Shift Name -->
                        <div class="form-group mb-3">
                            <label for="shift_name">Shift Rule Title</label>
                            <input type="text" class="form-control" id="shift_name" name="shift_name"
                                value="{{ old('shift_name') }}" required>
                        </div>

                        <!-- Time In -->
                        <div class="form-group mb-3">
                            <label for="time_in_after">Apply rule if Time In is after (minutes):</label>
                            <input type="number" class="form-control" id="time_in_after" name="time_in_after" min="0"
                                value="{{ old('time_in_after') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="time_out_after">Apply rule if Time Out is before (minutes):</label>
                            <input type="number" class="form-control" id="time_out_after" name="time_out_after" min="0"
                                value="{{ old('time_out_after') }}" required>
                        </div>


                        <!-- Action on Exceeding Time Out -->
                        <div class="form-group mb-3">
                            <label>What to do if exceeded Time Out Max?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="deduct_salary" name="deduct_salary">
                                <label class="form-check-label" for="deduct_salary">
                                    Deducted from Salary
                                </label>
                            </div>
                            <div id="deduct_hours" class="mt-2" style="display: none;">
                                <label for="hour_range">Select Number of Hours to Deduct:</label>
                                <input type="number" class="form-control" id="hour_range" name="hour_range" min="1" max="8">
                            </div>

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="give_warning" name="give_warning">
                                <label class="form-check-label" for="give_warning">
                                    Give Warning
                                </label>
                            </div>
                            <div id="warning_description" class="mt-2" style="display: none;">
                                <label for="warning_text">Description:</label>
                                <textarea class="form-control" id="warning_text" name="warning_text" rows="3"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Create Shift Rule</button>
                        <a href="{{ route('shifts-rules.store') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const deductSalary = document.getElementById("deduct_salary");
            const giveWarning = document.getElementById("give_warning");
            const deductHours = document.getElementById("deduct_hours");
            const warningDescription = document.getElementById("warning_description");

            function toggleFields() {
                deductHours.style.display = deductSalary.checked ? "block" : "none";
                warningDescription.style.display = giveWarning.checked ? "block" : "none";
            }

            deductSalary.addEventListener("change", toggleFields);
            giveWarning.addEventListener("change", toggleFields);
        });
    </script>
@endsection