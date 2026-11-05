<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Site - Attendance System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold mb-1">
                        Edit Site
                    </h1>

                    <p class="text-muted mb-0">
                        Update site information.
                    </p>

                </div>

                <a href="{{ route('sites.index') }}"
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
                          action="{{ route('sites.update', $site) }}">

                        @csrf

                        @method('PUT')


                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Site Name
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control form-control-lg"
                                   value="{{ old('name', $site->name) }}"
                                   required>

                        </div>


                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Site Code
                            </label>

                            <input type="text"
                                   name="code"
                                   class="form-control form-control-lg"
                                   value="{{ old('code', $site->code) }}"
                                   required>

                        </div>


                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Status
                            </label>

                            <select name="active"
                                    class="form-select form-select-lg"
                                    required>

                                <option value="1"
                                    {{ old('active', $site->active) == '1' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="0"
                                    {{ old('active', $site->active) == '0' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                            </select>

                        </div>


                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary btn-lg px-4">

                                Save Changes

                            </button>

                            <a href="{{ route('sites.index') }}"
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
