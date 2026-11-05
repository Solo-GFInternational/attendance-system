<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Employee - Attendance System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold mb-1">
                        Edit Employee
                    </h1>

                    <p class="text-muted mb-0">
                        Update employee information.
                    </p>

                </div>

                <a href="{{ route('employees.index') }}"
                   class="btn btn-outline-secondary">

                    ← Back

                </a>

            </div>


            @if ($errors->any())

                <div class="alert alert-danger">

                    <strong>Please correct the following:</strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <form method="POST"
                          action="{{ route('employees.update', $employee) }}">

                        @csrf
                        @method('PUT')


                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Employee ID
                            </label>

                            <input type="text"
                                   name="employee_code"
                                   class="form-control form-control-lg"
                                   value="{{ old('employee_code', $employee->employee_code) }}"
                                   required>

                        </div>


                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Employee Name
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control form-control-lg"
                                   value="{{ old('name', $employee->name) }}"
                                   required>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-4">

                                <label class="form-label fw-semibold">
                                    Department
                                </label>

                                <input type="text"
                                       name="department"
                                       class="form-control form-control-lg"
                                       value="{{ old('department', $employee->department) }}">

                            </div>


                            <div class="col-md-6 mb-4">

                                <label class="form-label fw-semibold">
                                    Designation
                                </label>

                                <input type="text"
                                       name="designation"
                                       class="form-control form-control-lg"
                                       value="{{ old('designation', $employee->designation) }}">

                            </div>

                        </div>


                        <div class="mb-4">

    <label class="form-label fw-semibold">
        Site
    </label>

    <select name="site_id"
            class="form-select form-select-lg"
            required>

        <option value="">
            -- Select Site --
        </option>

        @foreach($sites as $site)

            <option value="{{ $site->id }}"
                {{ old('site_id', $employee->site_id) == $site->id ? 'selected' : '' }}>

                {{ $site->name }}

            </option>

        @endforeach

    </select>

</div>
<div class="mb-4">
    <label class="form-label fw-semibold">
        Status
    </label>

    <select name="status"
            class="form-select form-select-lg"
            required>
        <option value="Active"
            {{ old('status', $employee->status) === 'Active' ? 'selected' : '' }}>
            Active
        </option>

        <option value="Inactive"
            {{ old('status', $employee->status) === 'Inactive' ? 'selected' : '' }}>
            Inactive
        </option>
    </select>
</div>

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary btn-lg px-4">

                                Save Changes

                            </button>

                            <a href="{{ route('employees.index') }}"
                               class="btn btn-light btn-lg px-4">

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>