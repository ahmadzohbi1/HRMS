@extends('layouts.master')

@section('title')
    Warnings
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Warnings
    @endslot
    @slot('li_2')
    {{ route('shifts.index') }}
    @endslot
    @slot('title')
    Warnings List
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-end mb-4" id="action_btns">
                        <a href="{{ route('warnings.create') }}" class="btn btn-rounded btn-success waves-effect waves-light"
                            style="margin-right: 10px;">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i> Add Warrning
                        </a>
                    </div>

                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead>
                            <tr class="table-light">
                                <th>Warning Id</th>
                                <th>Employee Name</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($warnings as $data)
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->employee->name }}</td>
                                    <td>{{ $data->warning_title }}</td>
                                    <td>{{ $data->warning_description }}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>
                                        <a href="{{ route('warnings.edit',$data->id) }}" class="btn btn-warning btn-md">Edit</a>
                                        <a href="{{ route('warnings.show',$data->id) }}" class="btn btn-primary btn-md">Show</a>
                                        <form action="{{ route('warnings.destroy', $data->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this warning?');">
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