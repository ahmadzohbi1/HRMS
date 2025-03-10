@extends('layouts.master')

@section('title')
    Companies List
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Companies
    @endslot
    @slot('li_2')
    {{ route('admins.index') }}
    @endslot
    @slot('title')
    Companies List
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-4" id="action_btns">
                        <div class="btn-group mx-3">
                            <!-- <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">@lang('translation.actions') <i
                                    class="mdi mdi-chevron-down"></i></button> -->
                        </div>
                        <a href="{{ route('companies.create') }} "
                            class="btn btn-rounded btn-success waves-effect waves-light"><i
                                class="bx bx-plus font-size-16 me-2 align-middle"></i>Add Company
                        </a>
                    </div>
                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Ban</th>
                                <th>Created_at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Utopia</td>
                                <td>info@utopialebanon.org</td>
                                <td>71835077</td>
                                <td>Ban</td>
                                <td>25-02-2025</td>
                                <td>do actions</td>
                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection