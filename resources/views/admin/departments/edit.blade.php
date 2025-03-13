@extends('layouts.master')

@section('title')
Edit Department
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Edit Department
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

                <!-- Form for editing the department -->
                <form action="{{ route('departments.update', $department->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div class="row mb-4">
                        <label for="name" class="col-sm-3 col-form-label">Department Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="name" name="name" 
                                value="{{ old('name', $department->name) }}" required>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row justify-content-end">
                        <div class="col-sm-9">
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
