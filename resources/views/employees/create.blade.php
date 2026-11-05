<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Employee - Attendance System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<div class="container py-5">

    <div class="mb-4">

        <h1>Add Employee</h1>

        <p class="text-muted">
            Add a new employee to the attendance system.
        </p>

    </div>

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Please fix the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card shadow-sm">

        <div class="card-body p-4">

            <form method="POST"
                  action="{{ route('employees.store') }}">

                @csrf


                <div class="mb-3">

                    <label class="form-label">
                        Employee ID
                    </label>

                    <input type="text"
                           name="employee_code"
                           class="form-control form-control-lg"
                           placeholder="EMP001"
                           value="{{ old('employee_code') }}"
                           required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Employee Name
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control form-control-lg"
                           placeholder="Employee Name"
                           value="{{ old('name') }}"
                           required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Department
                    </label>

                    <input type="text"
                           name="department"
                           class="form-control form-control-lg"
                           placeholder="IT"
                           value="{{ old('department') }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Designation
                    </label>

                    <input type="text"
                           name="designation"
                           class="form-control form-control-lg"
                           placeholder="IT Officer"
                           value="{{ old('designation') }}">

                </div>


                <div class="mb-4">

    <label class="form-label">
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
                {{ old('site_id') == $site->id ? 'selected' : '' }}>

                {{ $site->name }}

            </option>

        @endforeach

    </select>

</div>

<div class="mb-4">
    <label class="form-label">
        Status
    </label>

    <select
        name="status"
        class="form-select form-select-lg"
        required
    >
        <option value="Active"
            {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>
            Active
        </option>

        <option value="Inactive"
            {{ old('status') === 'Inactive' ? 'selected' : '' }}>
            Inactive
        </option>
    </select>
</div>


                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-success btn-lg">
                        Save Employee
                    </button>

                    <a href="{{ route('employees.index') }}"
                       class="btn btn-secondary btn-lg">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>