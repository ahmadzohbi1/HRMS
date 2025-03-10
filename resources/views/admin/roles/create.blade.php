@extends('layouts.master')

@section('title', 'Add New Role')

@section('css')
<!-- Add any required styles for your form here -->
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1')
        Roles
    @endslot
    @slot('li_2')
        {{ route('roles.index') }}
    @endslot
    @slot('title')
        Add New Role
    @endslot
@endcomponent

<!-- Add New Role Form -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <!-- Role Name Input -->
                    <div class="row mb-4">
                        <label for="role_name" class="col-sm-3 col-form-label">Role Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="role_name" name="role" class="form-control" required>
                            @error('role')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div class="row mb-4">
                        <label class="col-sm-3 col-form-label">Permissions</label>
                        <div class="col-sm-9">
                            <div class="modal-body">
                                <button type="button" id="select-all" class="btn btn-l  btn-primary waves-effect waves-light me-1">Select All</button>
                                <button type="button" id="deselect-all" class="btn btn-l btn-warning waves-effect waves-light me-1">Deselect All</button>
                                
                                <div class="row" style="margin-top:20px">
                                    
                                    <!-- Permission Categories -->
                                    @php
                                        $categories = [
                                            'companies' => 'Companies Permissions',
                                            'admins' => 'Admin Permissions', 
                                            'roles' => 'Role Permissions', 
                                            'employees' => 'Employee Permissions',
                                            'departments' => 'Department Permissions',
                                            'positions' => 'Position Permissions',
                                            'hour_rate' => 'Hour Rate Permissions',
                                            'salaries' => 'Salaries Permissions',
                                            'shifts' => 'shifts Permissions',
                                        ];
                                    @endphp

                                    @foreach($categories as $category => $label)
                                        <div class="col-md-12 mb-4">
                                            <h5>{{ $label }}</h5>
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                    @if(str_contains($permission->name, $category))
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}">
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row justify-content-end">
                        <div class="col-sm-9">
                            <button type="submit" class="btn btn-success">Save Role</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input.permission-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });

    document.getElementById('deselect-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input.permission-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    });
</script>

@endsection

@section('script')
<!-- Optional JS for permissions if needed -->
@endsection
