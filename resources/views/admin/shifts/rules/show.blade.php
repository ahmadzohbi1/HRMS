@extends('layouts.master')

@section('title', 'Shift Rules')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Shifts @endslot
        @slot('li_2') {{ route('shifts.index') }} @endslot
        @slot('title') Shift Rules: {{ $shift->shift_name }} @endslot
    @endcomponent

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    <div class="card-body">
                        <!-- Title and shift details -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-primary">{{ $shift->shift_name }}</h3>
                            <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary btn-sm">Back to Shifts</a>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Time In:</strong> <span class="text-muted">{{ $shift->time_in }}</span></p>
                                <p class="mb-1"><strong>Time Out:</strong> <span class="text-muted">{{ $shift->time_out }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Shift Type:</strong> <span class="badge bg-info">{{ ucfirst($shift->shift_type) }}</span></p>
                            </div>
                        </div>

                        <!-- Shift Rules Section -->
                        <h4 class="text-secondary mb-3">Shift Rules</h4>

                        @if($shift->shiftRules->isNotEmpty())
                            <div class="row">
                                @foreach($shift->shiftRules as $rule)
                                    <div class="col-md-6 mb-4">
                                        <div class="card shadow-sm border-0 rounded-lg">
                                            <div class="card-body">
                                                <h5 class="card-title text-dark">{{ $rule->shift_title }}</h5>
                                                <p><strong>Time In Apply:</strong> <span class="text-muted">{{ $rule->time_in_apply }} mins</span></p>
                                                <p><strong>Time Out Apply:</strong> <span class="text-muted">{{ $rule->time_out_apply }} mins</span></p>
                                                <p><strong>Deduct Hours:</strong> <span class="text-muted">{{ $rule->deduct_hours }} hours</span></p>
                                                <p><strong>Day Hours Deduction:</strong> <span class="text-muted">{{ $rule->day_hours_deduction }} hours</span></p>
                                                <p><strong>Warning:</strong> <span class="badge {{ $rule->give_warning ? 'bg-danger' : 'bg-success' }}">{{ $rule->give_warning ? 'Yes' : 'No' }}</span></p>
                                                <p><strong>Warning Description:</strong> <span class="text-muted">{{ $rule->warning_description ?? 'N/A' }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                No rules defined for this shift.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .badge {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .card-body p {
            font-size: 1rem;
            color: #6c757d;
        }

        .btn-outline-secondary {
            border-radius: 30px;
        }

        .shadow-lg {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('script')
    <script>
        // Optional JS for any future dynamic behavior if needed
    </script>
@endsection
