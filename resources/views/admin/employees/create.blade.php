@extends('layouts.master')

@section('title', 'Add New Employee')

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
    Add New Employee
    @endslot
    @endcomponent

    <!-- Add New Employee Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Name Input -->
                        <div class="row mb-4">
                            <label for="name" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="name" name="name" class="form-control" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="gender" class="col-sm-3 col-form-label">Gender</label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="male" name="gender" value="male" class="form-check-input"
                                        required>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="female" name="gender" value="female" class="form-check-input"
                                        required>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                                @error('gender')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="blood_type" class="col-sm-3 col-form-label">Blood Type</label>
                            <div class="col-sm-9">
                                <select id="blood_type" name="blood_type" class="form-select" required>
                                    <option value="" disabled selected>Select Blood Type</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
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
                                <input type="text" id="phone" name="phone" class="form-control" required>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="row mb-4">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" id="email" name="email" class="form-control" required>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="date_of_birth" class="col-sm-3 col-form-label">Date of Birth</label>
                            <div class="col-sm-9">
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" required>
                                @error('date_of_birth')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="place_of_birth" class="col-sm-3 col-form-label">Place of Birth</label>
                            <div class="col-sm-9">
                                <input type="text" id="place_of_birth" name="place_of_birth" class="form-control" required>
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
                                    <option value="LB">Lebanese</option>
                                    <option value="SY">Syrian</option>
                                    <option value="EG">Egyptian</option>
                                    <option value="JO">Jordanian</option>
                                    <option value="SA">Saudi Arabian</option>
                                    <option value="IQ">Iraqi</option>
                                    <option value="KW">Kuwaiti</option>
                                    <option value="AE">Emirati</option>
                                    <option value="OM">Omani</option>
                                    <option value="QA">Qatari</option>
                                    <option value="BH">Bahraini</option>
                                    <option value="YE">Yemeni</option>
                                    <option value="PS">Palestinian</option>
                                    <option value="SD">Sudanese</option>
                                    <option value="MA">Moroccan</option>
                                    <option value="DZ">Algerian</option>
                                    <option value="TN">Tunisian</option>
                                    <!-- Add other Arabic countries here -->
                                </select>
                                @error('nationality')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="marital_status" class="col-sm-3 col-form-label">Marital Status</label>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="single"
                                        value="SINGLE" required>
                                    <label class="form-check-label" for="single">Single</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="in_relationship"
                                        value="IN A RELATIONSHIP">
                                    <label class="form-check-label" for="in_relationship">In a Relationship</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="married"
                                        value="MARRIED">
                                    <label class="form-check-label" for="married">Married</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status" id="divorced"
                                        value="DIVORCED">
                                    <label class="form-check-label" for="divorced">Divorced</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marital_status"
                                        id="prefer_not_to_specify" value="PREFER NOT TO SPECIFY">
                                    <label class="form-check-label" for="prefer_not_to_specify">Prefer not to
                                        specify</label>
                                </div>
                            </div>
                        </div>


                        <!-- Address Input -->
                        <div class="row mb-4">
                            <label for="address" class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9">
                                <textarea id="address" name="address" class="form-control" required></textarea>
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

                        <!-- Display the uploaded image preview -->
                        <div class="row mb-4">
                            <label class="col-sm-3 col-form-label">Image Preview</label>
                            <div class="col-sm-9">
                                <img id="image-preview" src="" alt="Employee Image"
                                    style="max-width: 200px; max-height: 200px; display: none;" />
                            </div>
                        </div>


                        <!-- PIN Input -->
                        <div class="row mb-4">
                            <label for="pin" class="col-sm-3 col-form-label">Pin</label>
                            <div class="col-sm-9">
                                <input type="text" id="pin" name="pin" class="form-control" required>
                                @error('pin')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Position Input -->
                        <div class="row mb-4">
                            <label for="position" class="col-sm-3 col-form-label">Position</label>
                            <div class="col-sm-9">
                                <select id="position" name="position_id" class="form-control" required>
                                    <option value="">Select a Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
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
                                    <div class="form-check">
                                        <input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                            id="department_{{ $department->id }}" class="form-check-input" {{ in_array($department->id, old('department_ids', $employee->department_ids ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="department_{{ $department->id }}">
                                            {{ $department->name }}
                                        </label>
                                    </div>
                                @endforeach

                                @error('department_ids')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success">Save Employee</button>
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
                output.style.display = 'block'; // Show the image preview
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection