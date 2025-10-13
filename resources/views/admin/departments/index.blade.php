@extends('layouts.master')

@section('title')
    Departments List
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Departments
    @endslot
    @slot('li_2')

    @endslot
    @slot('title')
    Departments List
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{route('departments.create')}}"
                            class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>Add Department
                        </a>
                    </div>
                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Department Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department as $data)
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>
                                        <a href="{{ route('departments.edit', ($data->id)) }}"
                                            class="btn btn-warning btn-md">Edit</a>
                                        
                                        <form action="{{ route('departments.destroy', $data->id) }}" method="POST"
                                            class="d-inline" id="delete-department-{{ $data->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-md"
                                                onclick="confirmDelete('Department').then(result => { if(result) document.getElementById('delete-department-{{ $data->id }}').submit(); })">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

@endsection