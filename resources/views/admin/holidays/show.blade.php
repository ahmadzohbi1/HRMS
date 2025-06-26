@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Holidays @endslot
    @slot('title') Holiday Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-muted font-size-14">Holiday Name</h5>
                                <p class="font-size-16">{{ $holiday->name }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-muted font-size-14">Date</h5>
                                <p class="font-size-16">{{ $holiday->date->format('l, F j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-muted font-size-14">Day of Week</h5>
                                <p class="font-size-16">{{ $holiday->date->format('l') }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-muted font-size-14">Created At</h5>
                                <p class="font-size-16">{{ $holiday->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('holidays.index') }}" class="btn btn-secondary me-2">
                            <i class="bx bx-arrow-back font-size-16 me-2 align-middle"></i>
                            Back to List
                        </a>
                        <a href="{{ route('holidays.edit', $holiday->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit font-size-16 me-2 align-middle"></i>
                            Edit Holiday
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection