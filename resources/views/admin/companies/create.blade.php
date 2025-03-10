@extends('layouts.master')

@section('title', 'Add New Employee')

@section('css')
    <!-- Add any required styles for your form here -->
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Companies
    @endslot
    @slot('li_2')

    @endslot
    @slot('title')
    Add New Companies
    @endslot
    @endcomponent

    <!-- Add New Employee Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf
                        <!-- Name Input -->
                        <div class="row mb-4">
                            <label for="name" class="col-sm-3 col-form-label">Company Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="name" name="name" class="form-control" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Dashboard Username Input -->
                        <div class="row mb-4">
                            <label for="username" class="col-sm-3 col-form-label">Dashboard Username</label>
                            <div class="col-sm-9">
                                <input type="text" id="username" name="username" class="form-control" required>
                                @error('username')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="row mb-4">
                            <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9">
                                <input type="text" id="phone" name="phone" class="form-control" required>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="row mb-4">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" id="email" name="email" class="form-control" required>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Address Input -->
                        <div class="row mb-4">
                            <label for="address" class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9">
                                <textarea id="address" name="address" class="form-control" required></textarea>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- PIN Input -->
                        <div class="row mb-4">
                        <label for="pin" class="col-sm-3 col-form-label">Pin</label>
                        <div class="col-sm-9">
                            <input type="text" id="pin" name="pin" class="form-control" required>
                            @error('pin')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                        <!-- Submit Button -->
                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success">Save Employee</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection