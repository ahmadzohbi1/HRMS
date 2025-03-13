@extends('layouts.master')

@section('title')
    Shifts
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Shifts
    @endslot
    @slot('li_2')
    {{ route('shifts.index') }}
    @endslot
    @slot('title')
    Shifts List
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-end mb-4" id="action_btns">
                        <a href="{{ route('shifts.create') }}" class="btn btn-rounded btn-success waves-effect waves-light"
                            style="margin-right: 10px;">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i> Add Shift
                        </a>
                    </div>
                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead>
                            <tr class="table-light">
                                <th>Shift Type</th>
                                <th>Shift Name</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>For</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($shifts as $shift)
                                <tr>
                                    <td>{{ ucfirst($shift->shift_type) }}</td>
                                    <td>{{ $shift->shift_name }}</td>
                                    <td>{{ $shift->time_in }}</td>
                                    <td>{{ $shift->time_out }}</td>
                                    <td>
                                        @if($shift->shift_type == 'company')
                                            Company-wide
                                        @elseif($shift->shift_type == 'department')
                                            {{ $shift->department->name ?? 'N/A' }}
                                        @else
                                            {{ $shift->employee->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-warning btn-md">Edit</a>
                                        <a href="{{ route('shifts.rules.show', $shift->id) }}"
                                            class="btn btn-primary btn-md">View Rules</a>

                                        <!-- Delete Button with Confirmation -->
                                        <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this shift?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-md">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection