@extends('layouts.master')

@section('title')
    @lang('translation.user.add_user')
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Add New Admin
    @endslot
    @slot('li_2')
    Admins
    @endslot
    @slot('title')
    Admins
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

                    <!-- Form for creating a new user -->
                    <form action="{{ route('admins.store') }}" method="POST">
                        @csrf
                        <!-- Name Field -->
                        <div class="row mb-4">
                            <label for="name" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                                    required>
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="row mb-4">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                                    required>
                            </div>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="row mb-4">
                            <label for="password" class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="row mb-4">
                            <label for="password_confirmation" class="col-sm-3 col-form-label">Confirm Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>

                        <!-- Role Selection Field -->
                        <div class="row mb-4">
                            <label for="role" class="col-sm-3 col-form-label">Role</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="role_id" name="role_id" required>

                                    @foreach ($roles as $role)
                                        <!-- Exclude Super Admin role -->
                                        @if($role->name != 'Super Admin')
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <!-- Phone Field -->
                        <div class="row mb-4">
                            <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}"
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