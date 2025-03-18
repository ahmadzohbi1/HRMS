@extends('layouts.master')

@section('content')
@component('components.breadcrumb')
@slot('li_1') Vacations @endslot
@slot('title') View Vacation Request @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Vacation Request Details</h5>
                <dl class="row">
                    <dt class="col-sm-3">Employee:</dt>
                    <dd class="col-sm-9">{{ $vacation->employee->name }}</dd>

                    <dt class="col-sm-3">Vacation Type:</dt>
                    <dd class="col-sm-9">{{ $vacation->vacationType->name }}</dd>

                    <dt class="col-sm-3">Start Date:</dt>
                    <dd class="col-sm-9">{{ $vacation->start_date->format('Y-m-d') }}</dd>

                    <dt class="col-sm-3">End Date:</dt>
                    <dd class="col-sm-9">{{ $vacation->end_date->format('Y-m-d') }}</dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $vacation->status == 'approved' ? 'success' : ($vacation->status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($vacation->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">Reason:</dt>
                    <dd class="col-sm-9">{{ $vacation->reason }}</dd>

                    <dt class="col-sm-3">Created At:</dt>
                    <dd class="col-sm-9">{{ $vacation->created_at->format('Y-m-d H:i:s') }}</dd>

                    
                    
                </dl>
                <div class="mt-4">
                    <a href="{{ route('vacations.edit', $vacation->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('vacations.index') }}" class="btn btn-secondary">Back to List</a>
                    <form action="{{ route('vacations.destroy', $vacation->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vacation request?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // You can add any additional JavaScript here if needed
</script>
@endsection