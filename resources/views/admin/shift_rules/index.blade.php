@extends('layouts.master')

@section('title')
    Shifts Rules
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
    Shifts Rules List
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-end mb-4" id="action_btns">
                        <a href="{{ route('shifts-rules.create') }}"
                            class="btn btn-rounded btn-success waves-effect waves-light" style="margin-right: 10px;">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i> Add Shift Rule
                        </a>

                    </div>

                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead>
                            <tr class="table-light">
                                <th>#</th>
                                <th>Shift Name</th>
                                <th>Time In Max</th>
                                <th>Rule Type</th>
                                
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shifts_rules as $data)

                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->shift_title }}</td>
                                    <td>{{ $data->time_in_apply }} mins</td>
                                    <td>
                                        @if($data->deduct_hours == 1)
                                            Deducted from Salary
                                        @elseif($data->deduct_hours == 0)
                                            Warning
                                            @endif
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('shifts-rules.edit',($data->id)) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="" class="btn btn-primary btn-sm">View</a>
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