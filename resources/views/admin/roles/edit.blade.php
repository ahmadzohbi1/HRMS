@extends('layouts.master')

@section('title')
Roles
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
Edit Roles
@endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="needs-validation" novalidate action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-8">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="role" name="role" value="{{ old('role', $role->name) }}" required>
                            </div>

                            <div class="row mb-4">
                                <label class="col-sm-3 col-form-label">Permissions</label>
                                <div class="col-sm-9">
                                    <button type="button" id="select-all" class="btn btn-primary mb-3">Select All</button>
                                    <button type="button" id="deselect-all" class="btn btn-warning mb-3">Deselect All</button>
                                    <div class="modal-body">
                                        <div class="row">

                                        @php
                                         $categories = [
                                            'admins' => 'Admin Permissions', 
                                            'roles' => 'Role Permissions', 
                                            'employees' => 'Employee Permissions',
                                            'departments' => 'Department Permissions',
                                            'positions' => 'Position Permissions',
                                            'hour_rate' => 'Hour Rate Permissions',
                                            'salaries' => 'Salaries Permissions',
                                            'shifts' => 'Shifts Permissions',
                                            'shift_rules' => 'Shift_Rules Permissions',
                                            'warnings' => 'Warnings Permissions',
                                            'vacations' => 'Vacations Permissions',
                                            'holidays' => 'Holidays Permissions',
                                            'time log pin' => 'Time Log PIN Permissions',

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
                                                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
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

                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <div>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <!-- end card -->
    </div> <!-- end col -->
</div>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });

    document.getElementById('deselect-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    });
</script>


@endsection