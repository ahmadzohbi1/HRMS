@extends('layouts.master')

@section('title')
Employee Details
@endsection

@section('css')
<!-- Add required CSS for styling -->
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/libs/magnific-popup/magnific-popup.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Employees
@endslot
@slot('li_2')
{{ route('employees.index') }}
@endslot
@slot('title')
{{ $employee->name }}'s Profile
@endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12">
                        <h4 class="header-title mb-3">Employee Information</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <!-- Employee Information -->
                                    <tr>
                                        <th style="width: 150px;">Departments:</th>
                                    </tr>
                                    <tr>

                                        <td>{!! $employee->departments->pluck('name')->implode('<br>') ?? 'N/A' !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <!-- end row -->

                <!-- Back Button -->
                <div class="mt-3 text-end">
                    <a href="{{ route('employees.index') }}" class="btn btn-primary">
                        <i class="bx bx-arrow-back"></i> Back to Employees List
                    </a>
                </div>
            </div>
        </div>
        <!-- end card -->
    </div>
</div>
<!-- end row -->
@endsection

@section('script')
<!-- Optional JS for other functionalities -->
@endsection