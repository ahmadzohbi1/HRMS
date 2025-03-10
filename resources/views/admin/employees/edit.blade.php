@extends('layouts.master')

@section('title', 'Edit Employee - ' . $employee->name)

@section('css')
    <!-- Add any required styles for your form here -->
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
    Edit {{ $employee->name }}'s Profile
    @endslot
    @endcomponent

    <!-- Edit Employee Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name Input -->
                        <div class="row mb-4">
                            <label for="name" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="name" name="name" class="form-control"
                                    value="{{ old('name', $employee->name) }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Gender Input -->
                        <div class="row mb-4">
                            <label for="gender" class="col-sm-3 col-form-label">Gender</label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="male" name="gender" value="male" class="form-check-input" {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="female" name="gender" value="female" class="form-check-input" {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                                @error('gender')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Blood Type Input -->
                        <div class="row mb-4">
                            <label for="blood_type" class="col-sm-3 col-form-label">Blood Type</label>
                            <div class="col-sm-9">
                                <select id="blood_type" name="blood_type" class="form-select" required>
                                    <option value="" disabled selected>Select Blood Type</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodType)
                                        <option value="{{ $bloodType }}" {{ old('blood_type', $employee->blood_type) == $bloodType ? 'selected' : '' }}>{{ $bloodType }}</option>
                                    @endforeach
                                </select>
                                @error('blood_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="row mb-4">
                            <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9">
                                <input type="text" id="phone" name="phone" class="form-control"
                                    value="{{ old('phone', $employee->phone) }}" required>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="row mb-4">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', $employee->email) }}" required>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date of Birth Input -->
                        <div class="row mb-4">
                            <label for="date_of_birth" class="col-sm-3 col-form-label">Date of Birth</label>
                            <div class="col-sm-9">
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control"
                                    value="{{ old('date_of_birth', $employee->date_of_birth) }}" required>
                                @error('date_of_birth')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Place of Birth Input -->
                        <div class="row mb-4">
                            <label for="place_of_birth" class="col-sm-3 col-form-label">Place of Birth</label>
                            <div class="col-sm-9">
                                <input type="text" id="place_of_birth" name="place_of_birth" class="form-control"
                                    value="{{ old('place_of_birth', $employee->place_of_birth) }}" required>
                                @error('place_of_birth')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="nationality" class="col-sm-3 col-form-label">Nationality</label>
                            <div class="col-sm-9">
                                <select id="nationality" name="nationality" class="form-control" required>
                                    <option value="">Select Nationality</option>
                                    <option value="LB" {{ old('nationality', $employee->nationality) == 'LB' ? 'selected' : '' }}>Lebanese</option>
                                    <option value="SY" {{ old('nationality', $employee->nationality) == 'SY' ? 'selected' : '' }}>Syrian</option>
                                    <option value="EG" {{ old('nationality', $employee->nationality) == 'EG' ? 'selected' : '' }}>Egyptian</option>
                                    <option value="JO" {{ old('nationality', $employee->nationality) == 'JO' ? 'selected' : '' }}>Jordanian</option>
                                    <option value="SA" {{ old('nationality', $employee->nationality) == 'SA' ? 'selected' : '' }}>Saudi Arabian</option>
                                    <option value="IQ" {{ old('nationality', $employee->nationality) == 'IQ' ? 'selected' : '' }}>Iraqi</option>
                                    <option value="KW" {{ old('nationality', $employee->nationality) == 'KW' ? 'selected' : '' }}>Kuwaiti</option>
                                    <option value="AE" {{ old('nationality', $employee->nationality) == 'AE' ? 'selected' : '' }}>Emirati</option>
                                    <option value="OM" {{ old('nationality', $employee->nationality) == 'OM' ? 'selected' : '' }}>Omani</option>
                                    <option value="QA" {{ old('nationality', $employee->nationality) == 'QA' ? 'selected' : '' }}>Qatari</option>
                                    <option value="BH" {{ old('nationality', $employee->nationality) == 'BH' ? 'selected' : '' }}>Bahraini</option>
                                    <option value="YE" {{ old('nationality', $employee->nationality) == 'YE' ? 'selected' : '' }}>Yemeni</option>
                                    <option value="PS" {{ old('nationality', $employee->nationality) == 'PS' ? 'selected' : '' }}>Palestinian</option>
                                    <option value="SD" {{ old('nationality', $employee->nationality) == 'SD' ? 'selected' : '' }}>Sudanese</option>
                                    <option value="MA" {{ old('nationality', $employee->nationality) == 'MA' ? 'selected' : '' }}>Moroccan</option>
                                    <option value="DZ" {{ old('nationality', $employee->nationality) == 'DZ' ? 'selected' : '' }}>Algerian</option>
                                    <option value="TN" {{ old('nationality', $employee->nationality) == 'TN' ? 'selected' : '' }}>Tunisian</option>
                                </select>
                                @error('nationality')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <!-- Marital Status Input -->
                        <div class="row mb-4">
                            <label for="marital_status" class="col-sm-3 col-form-label">Marital Status</label>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="single"
                                        value="SINGLE" {{ old('marital_status', $employee->marital_status) == 'SINGLE' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="single">Single</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="in_relationship"
                                        value="IN A RELATIONSHIP" {{ old('marital_status', $employee->marital_status) == 'IN A RELATIONSHIP' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="in_relationship">In a Relationship</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="married"
                                        value="MARRIED" {{ old('marital_status', $employee->marital_status) == 'MARRIED' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="married">Married</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="divorced"
                                        value="DIVORCED" {{ old('marital_status', $employee->marital_status) == 'DIVORCED' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="divorced">Divorced</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status"
                                        id="prefer_not_to_specify" value="PREFER NOT TO SPECIFY" {{ old('marital_status', $employee->marital_status) == 'PREFER NOT TO SPECIFY' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prefer_not_to_specify">Prefer not to
                                        specify</label>
                                </div>
                            </div>
                        </div>

                        <!-- Address Input -->
                        <div class="row mb-4">
                            <label for="address" class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9">
                                <textarea id="address" name="address" class="form-control"
                                    required>{{ old('address', $employee->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="image" class="col-sm-3 col-form-label">Upload Image</label>
                            <div class="col-sm-9">
                                <input type="file" id="image" name="image" class="form-control" accept="image/*"
                                    onchange="previewImage(event)">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Display the uploaded or old image -->
                        <div class="row mb-4">
                            <label class="col-sm-3 col-form-label">Current Image</label>
                            <div class="col-sm-9">
                                <img id="image-preview"
                                    src="{{ old('image', $employee->image_url ? asset('uploads/' . $employee->image_url) : '') }}"
                                    alt="Employee Image" style="max-width: 200px; max-height: 200px;" />
                            </div>
                        </div>

                        <!-- PIN Input -->
                        <div class="row mb-4">
                            <label for="pin" class="col-sm-3 col-form-label">PIN</label>
                            <div class="col-sm-9">
                                <input type="text" id="pin" name="pin" class="form-control"
                                    value="{{ old('pin', $employee->pin) }}" required>
                                @error('pin')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="position" class="col-sm-3 col-form-label">Position</label>
                            <div class="col-sm-9">
                                <select id="position" name="position_id" class="form-control" required>
                                    <option value="" disabled selected>Select Position</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Department Input -->
                        <div class="row mb-4">
                            <label for="department" class="col-sm-3 col-form-label">Department</label>
                            <div class="col-sm-9">
                                @foreach ($departments as $department)
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                            class="form-check-input" {{ $ink = in_array($department->id, old('departments', $employee->departments->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="department">{{ $department->name }}</label>
                                    </div>
                                @endforeach
                                @error('departments')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row justify-content-end">
                           
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        // JavaScript function to preview the selected image
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('image-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection