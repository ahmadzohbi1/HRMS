@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Holidays @endslot
    @slot('title') Holidays List @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('holidays.create') }}"
                            class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>
                            Add Holiday
                        </a>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Holiday Name</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($holidays as $holiday)
                                <tr>
                                    <td>{{ $holiday->id }}</td>
                                    <td>{{ $holiday->name }}</td>
                                    <td>{{ $holiday->date->format('Y-m-d') }}</td>
                                    <td>{{ $holiday->date->format('l') }}</td>
                                    <td>
                                        <a href="{{ route('holidays.edit', $holiday->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this holiday?')">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
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

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection