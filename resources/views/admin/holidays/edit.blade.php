@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Holidays @endslot
    @slot('title') Edit Holiday @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('holidays.update', $holiday->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Holiday Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $holiday->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('holidays.index') }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save font-size-16 me-2 align-middle"></i>
                                Update Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection