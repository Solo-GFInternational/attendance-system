<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Site Management - Attendance System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold mb-1">
                Site Management
            </h1>

            <p class="text-muted mb-0">
                Manage attendance sites and locations.
            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('employees.index') }}"
               class="btn btn-outline-secondary">

                Employees

            </a>

            <a href="{{ route('sites.create') }}"
               class="btn btn-primary">

                + Add Site

            </a>

        </div>

    </div>


    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


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


    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">
                                Site
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Employees
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end px-4">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($sites as $site)

                            <tr>

                                <td class="px-4">

                                    <div class="fw-semibold">
                                        {{ $site->name }}
                                    </div>

                                </td>


                                <td>

                                    <span class="badge text-bg-secondary">
                                        {{ $site->code }}
                                    </span>

                                </td>


                                <td>

                                    {{ $site->employees_count }}

                                </td>


                                <td>

                                    @if ($site->active)

                                        <span class="badge text-bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td class="text-end px-4">

                                    <div class="d-flex justify-content-end gap-2">

                                        <a href="{{ route('sites.edit', $site) }}"
                                           class="btn btn-sm btn-outline-primary">

                                            Edit

                                        </a>


                                        <form method="POST"
                                              action="{{ route('sites.toggle-status', $site) }}">

                                            @csrf

                                            @method('PATCH')

                                            @if ($site->active)

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger">

                                                    Disable

                                                </button>

                                            @else

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-success">

                                                    Enable

                                                </button>

                                            @endif

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center py-5 text-muted">

                                    No sites have been added yet.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>

</html>