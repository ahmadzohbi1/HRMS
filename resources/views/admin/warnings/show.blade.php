@extends('layouts.master')

@section('title')
    View Warning
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
    {{ route('warnings.index') }}
    @endslot
    @slot('title')
    View Warning
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">Employee</label>
                                <p>{{ $warning->employee->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warning_title">Warning Title</label>
                                <p>{{ $warning->warning_title ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="warning_description">Description</label>
                                <p>{{ $warning->warning_description ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="created_at">Created At</label>
                                <p>{{ $warning->created_at->format('d M Y, H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="updated_at">Last Updated</label>
                                <p>{{ $warning->updated_at->format('d M Y, H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('warnings.index') }}" class="btn btn-primary">Back to List</a>
                        <a href="{{ route('warnings.edit', $warning->id) }}" class="btn btn-warning">Edit Warning</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
