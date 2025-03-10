@extends('layouts.master')

@section('title')
Add New Department
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Add New Department
@endslot
@slot('li_2')
Departments
@endslot
@slot('title')
Departments
@endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <!-- Display validation errors if any -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form for creating a new department -->
                <form action="{{ route('departments.store') }}" method="POST">
                    @csrf
                    <!-- Name Field -->
                    <div class="row mb-4">
                        <label for="name" class="col-sm-3 col-form-label">Department Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                                required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row justify-content-end">
                        <div class="col-sm-9">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
