@extends('layouts.master')

@section('title')
Edit Admin
@endsection

@section('css')
<!-- DataTables -->
<link href="/assets/libs/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Admins
@endslot
@slot('li_2')
{{ route('admins.edit', $user->id) }}
@endslot
@slot('title')
Edit Admin
@endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Edit User Form -->
                <form class="needs-validation" novalidate action="{{ route('admins.update', $user->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-xl-8">
                            <!-- Name Field -->
                            <div class="row mb-4">
                                <label for="name" class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required>
                                    <div class="valid-feedback">
                                        @lang('validation.good')
                                    </div>
                                    <div class="invalid-feedback">
                                        @lang('validation.required', ['attribute' => __('translation.user.name')])
                                    </div>
                                </div>
                            </div>

                            <!-- Email Field -->
                            <div class="row mb-4">
                                <label for="email"
                                    class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                    <div class="valid-feedback">
                                        @lang('validation.good')
                                    </div>
                                    <div class="invalid-feedback">
                                        @lang('validation.required', ['attribute' => __('translation.user.email')])
                                    </div>
                                </div>
                            </div>

                            <!-- Role Field -->
                            <div class="row mb-4">
                                <label for="role" class="col-sm-3 col-form-label">Role</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" id="role_id" name="role_id" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id',$user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Phone Field -->
                            <div class="row mb-4">
                                <label for="phone"
                                    class="col-sm-3 col-form-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $user->phone) }}" required>
                                    <div class="valid-feedback">
                                        @lang('validation.good')
                                    </div>
                                    <div class="invalid-feedback">
                                        @lang('validation.required', ['attribute' => __('translation.user.phone')])
                                    </div>
                                </div>
                            </div>

                            <!-- Password Field (optional) -->
                            <div class="row mb-4">
                                <label for="password" class="col-sm-3 col-form-label">New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" name="password">
                                    <div class="valid-feedback">
                                        @lang('validation.good')
                                    </div>
                                    <div class="invalid-feedback">
                                        @lang('validation.required', ['attribute' => __('translation.user.password')])
                                    </div>
                                    <small class="form-text text-muted">
                                        Leave blank if you do not wish to change the password.
                                    </small>
                                </div>
                            </div>

                            <!-- Password Confirmation Field (optional) -->
                            <div class="row mb-4">
                                <label for="password_confirmation" class="col-sm-3 col-form-label">Confirm Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    <div class="valid-feedback">
                                        @lang('validation.good')
                                    </div>
                                    <div class="invalid-feedback">
                                        @lang('validation.required', ['attribute' => __('translation.user.password_confirmation')])
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
@endsection

@section('script')
<!-- Required DataTables JS -->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
@endsection
