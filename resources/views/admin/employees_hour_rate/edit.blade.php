@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Hour Rate for {{ $hourRate->employee->name }}</h4>
                <form action="{{ route('hour_rate.update', $hourRate->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="hour_rate" class="form-label">Hour Rate:</label>
                        <input type="number" step="0.01" name="hour_rate" id="hour_rate" class="form-control" value="{{ $hourRate->hour_rate }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency:</label>
                        <input type="text" name="currency" id="currency" class="form-control" value="{{ $hourRate->currency }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection