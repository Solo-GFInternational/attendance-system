<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Import Employees</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f5f7fa;
        }

        .import-card {
            max-width: 750px;
            margin: 60px auto;
            border-radius: 16px;
        }

        .upload-area {
            border: 2px dashed #ced4da;
            border-radius: 14px;
            padding: 40px 25px;
            text-align: center;
            background: #fafbfc;
        }

        .upload-area:hover {
            border-color: #0d6efd;
            background: #f5f9ff;
        }
    </style>
</head>

<body class="admin-ui">

<div class="container-fluid px-4 pt-4">
    @include('partials.admin-nav')
</div>

<div class="container px-4">

    <div class="card border-0 shadow-sm import-card">

        <div class="card-body p-4 p-md-5">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        📥 Import Employees
                    </h2>

                    <p class="text-muted mb-0">
                        Upload a CSV file to add or update employees.
                    </p>

                </div>

                <a href="{{ route('employees.index') }}"
                   class="btn btn-outline-secondary">

                    ← Back

                </a>

            </div>


            @if(session('error'))

                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>

            @endif


            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>Please fix the following:</strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <div class="alert alert-info">

                <strong>Required CSV columns:</strong>

                <ul class="mb-0 mt-2">

                    <li>Employee ID</li>

                    <li>Employee Name</li>

                    <li>Department</li>

                    <li>Designation</li>

                    <li>
                        Site &ndash; the same site name or code as on the
                        <a href="{{ route('sites.index') }}">Sites</a> page
                    </li>

                </ul>

                <div class="mt-3">
                    @if ($sites->isEmpty())
                        <strong>There are no active sites yet.</strong> Add your sites before importing.
                    @else
                        <strong>Sites you can use:</strong>
                        {{ $sites->map(fn ($site) => $site->name === $site->code ? $site->name : $site->name . ' (' . $site->code . ')')->implode(', ') }}
                    @endif
                </div>

            </div>


            <form method="POST"
                  action="{{ route('employees.import.store') }}"
                  enctype="multipart/form-data">

                @csrf


                <div class="upload-area mb-4">

                    <div class="fs-1 mb-3">
                        📄
                    </div>

                    <h5 class="fw-bold">
                        Select your employee CSV file
                    </h5>

                    <p class="text-muted">
                        Export your Excel spreadsheet as CSV and upload it here.
                    </p>

                    <input type="file"
                           name="csv_file"
                           class="form-control"
                           accept=".csv,.txt"
                           required>

                    <small class="text-muted d-block mt-3">
                        Maximum file size: 10 MB
                    </small>

                </div>


                <button type="submit"
                        class="btn btn-primary btn-lg w-100">

                    📥 Import Employees

                </button>

            </form>


            <hr class="my-4">


            <div>

                <h6 class="fw-bold">
                    How duplicates work
                </h6>

                <p class="text-muted mb-0">

                    If an Employee ID already exists, the system will update
                    the employee's name, department, designation and site instead
                    of creating a duplicate. If Site is blank, an existing employee
                    keeps their current site. Existing employee status will not be
                    changed. Rows with a missing or unknown site are skipped and
                    listed after the import.

                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>