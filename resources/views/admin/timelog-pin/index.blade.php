@extends('layouts.master')

@section('title')
    Time Log PIN Management
@endsection

@section('css')
    <style>
        .pin-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .pin-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .pin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .pin-header i {
            font-size: 64px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .pin-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .pin-status.active {
            background: rgba(52, 195, 143, 0.2);
            color: #34c38f;
        }

        .pin-status.inactive {
            background: rgba(244, 106, 106, 0.2);
            color: #f46a6a;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .pin-input-group {
            max-width: 300px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Dashboard @endslot
        @slot('title') Time Log PIN Management @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- PIN Status Card -->
            <div class="card pin-card mb-4">
                <div class="pin-header">
                    <i class="bx bx-lock-alt"></i>
                    <h3 class="mb-2">Time Log PIN</h3>
                    <p class="mb-3">Secure access to the employee time logging system</p>
                    @if($pin)
                        <span class="pin-status active">
                            <i class="bx bx-check-circle"></i> PIN Configured
                        </span>
                    @else
                        <span class="pin-status inactive">
                            <i class="bx bx-x-circle"></i> No PIN Set
                        </span>
                    @endif
                </div>

                <div class="card-body p-4">
                    @if($pin)
                        <!-- PIN Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon bg-primary bg-soft text-primary me-3">
                                            <i class="bx bx-calendar"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 13px;">Last Updated</p>
                                            <h5 class="mb-0">{{ $pin->updated_at->format('M d, Y') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon bg-success bg-soft text-success me-3">
                                            <i class="bx bx-user"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1" style="font-size: 13px;">Updated By</p>
                                            <h5 class="mb-0">{{ optional($pin->updater)->name ?? 'System' }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-box">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Security Note:</strong> The time log screen will require this PIN for access. The PIN lock resets daily for security.
                        </div>

                        <!-- Reset PIN Form -->
                        <h5 class="mb-3"><i class="bx bx-reset me-2"></i>Reset PIN</h5>
                        <form action="{{ route('timelog-pin.reset') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_pin" class="form-label">New PIN</label>
                                    <input type="password" 
                                           class="form-control @error('new_pin') is-invalid @enderror" 
                                           id="new_pin" 
                                           name="new_pin" 
                                           placeholder="Enter 4-digit PIN"
                                           maxlength="4"
                                           pattern="[0-9]{4}"
                                           inputmode="numeric"
                                           required>
                                    @error('new_pin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Must be exactly 4 digits</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="new_pin_confirmation" class="form-label">Confirm PIN</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_pin_confirmation" 
                                           name="new_pin_confirmation" 
                                           placeholder="Re-enter PIN"
                                           maxlength="4"
                                           pattern="[0-9]{4}"
                                           inputmode="numeric"
                                           required>
                                    <small class="text-muted">Enter the same PIN again</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-gradient">
                                <i class="bx bx-reset me-2"></i>Reset PIN
                            </button>
                        </form>

                    @else
                        <!-- Create PIN Form -->
                        <div class="info-box">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>No PIN Set:</strong> Create a 4-digit PIN to secure the time log system. Employees will need this PIN to access the time logging screen.
                        </div>

                        <h5 class="mb-3"><i class="bx bx-plus-circle me-2"></i>Create New PIN</h5>
                        <form action="{{ route('timelog-pin.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pin" class="form-label">PIN</label>
                                    <input type="password" 
                                           class="form-control @error('pin') is-invalid @enderror" 
                                           id="pin" 
                                           name="pin" 
                                           placeholder="Enter 4-digit PIN"
                                           maxlength="4"
                                           pattern="[0-9]{4}"
                                           inputmode="numeric"
                                           required>
                                    @error('pin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Must be exactly 4 digits</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pin_confirmation" class="form-label">Confirm PIN</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="pin_confirmation" 
                                           name="pin_confirmation" 
                                           placeholder="Re-enter PIN"
                                           maxlength="4"
                                           pattern="[0-9]{4}"
                                           inputmode="numeric"
                                           required>
                                    <small class="text-muted">Enter the same PIN again</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-gradient">
                                <i class="bx bx-save me-2"></i>Create PIN
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- How It Works -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bx bx-help-circle me-2"></i>How It Works
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-20">
                                        <i class="bx bx-lock-alt"></i>
                                    </span>
                                </div>
                                <h6>Secure Access</h6>
                                <p class="text-muted" style="font-size: 13px;">
                                    The time log screen is protected by a 4-digit PIN that must be entered to access.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-success bg-soft text-success font-size-20">
                                        <i class="bx bx-calendar-check"></i>
                                    </span>
                                </div>
                                <h6>Daily Reset</h6>
                                <p class="text-muted" style="font-size: 13px;">
                                    The PIN unlock status resets each day for enhanced security.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-warning bg-soft text-warning font-size-20">
                                        <i class="bx bx-shield"></i>
                                    </span>
                                </div>
                                <h6>Admin Control</h6>
                                <p class="text-muted" style="font-size: 13px;">
                                    Only administrators can view and manage the time log PIN settings.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Validate PIN inputs to only accept numbers
        document.querySelectorAll('input[type="password"][name*="pin"]').forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
@endsection

