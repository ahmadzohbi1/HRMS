@extends('layouts.master')

@section('title')
    Positions List
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Positions
    @endslot
    @slot('li_2')

    @endslot
    @slot('title')
    Positions List
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{route('positions.create')}}"
                            class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>Add Position
                        </a>
                    </div>
                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead class="table-light">
                            <tr style="border-color: #cad3dc !important;">
                                <th>#</th>
                                <th>Positon Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            @foreach ($postions_data as $data)
                                <tr style="border-color: #cad3dc !important;">
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>

                                        <form action="{{ route('positions.delete', $data->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i></i> Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach

                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

@endsection