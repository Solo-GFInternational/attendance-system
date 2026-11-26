<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme | Attendance Management System</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }

        .theme-option {
            position: relative;
            display: flex;
            flex-direction: column;
            margin: 0;
            border: 2px solid var(--gf-border);
            border-radius: 14px;
            background: var(--gf-surface);
            overflow: hidden;
            cursor: pointer;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .theme-option:hover {
            border-color: var(--gf-border-strong);
        }

        .theme-option:has(input:checked) {
            border-color: var(--gf-primary);
            box-shadow: 0 0 0 3px var(--gf-primary-soft);
        }

        .theme-option:has(input:focus-visible) {
            outline: 2px solid var(--gf-primary);
            outline-offset: 2px;
        }

        .theme-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .theme-preview {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            gap: 4px;
            height: 118px;
            padding: 14px 16px;
            color: #ffffff;
            background-size: 120px 120px, cover;
            background-repeat: repeat, no-repeat;
        }

        .theme-preview-emoji {
            font-size: 30px;
            line-height: 1;
        }

        .theme-preview-greeting {
            font-size: 13px;
            font-weight: 600;
            opacity: .92;
        }

        .theme-body {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 14px 16px 16px;
        }

        .theme-name {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--gf-text);
        }

        .theme-check {
            display: none;
            margin-left: auto;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--gf-primary);
            color: #ffffff;
            place-items: center;
        }

        .theme-check svg {
            width: 13px;
            height: 13px;
        }

        .theme-option:has(input:checked) .theme-check {
            display: grid;
        }

        .theme-description {
            font-size: 13px;
            color: var(--gf-muted);
        }
    </style>
</head>

<body class="admin-ui">

<div class="container-fluid py-4 px-4">

    @include('partials.admin-nav')

    <div class="card mb-4">
        <div class="card-body p-4">

            <h2 class="fw-bold mb-1">Theme</h2>
            <p class="text-muted mb-4">
                Choose a festive look for the whole system. It shows on every admin page and every site tablet
                as soon as you save. Each person can still switch between light and dark mode on their own device.
            </p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('theme.update') }}">
                @csrf
                @method('PUT')

                <div class="theme-grid" role="radiogroup" aria-label="Theme">
                    @foreach ($themes as $key => $theme)
                        <label class="theme-option">
                            <input type="radio" name="theme" value="{{ $key }}" @checked(old('theme', $currentKey) === $key)>

                            <div class="theme-preview" style="background-image: {{ $theme['patternUrl'] ? $theme['patternUrl'] . ', ' : '' }}{{ $theme['hero'] }};">
                                <span class="theme-preview-emoji" aria-hidden="true">{{ $theme['emoji'] }}</span>
                                <span class="theme-preview-greeting">{{ $theme['greeting'] ?? 'Goodman Fielder Attendance System' }}</span>
                            </div>

                            <div class="theme-body">
                                <div class="theme-name">
                                    {{ $theme['name'] }}
                                    @if ($key === $currentKey)
                                        <span class="badge bg-success-subtle text-success-emphasis">Showing now</span>
                                    @endif
                                    <span class="theme-check" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    </span>
                                </div>
                                <div class="theme-description">{{ $theme['description'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                    <button type="submit" class="btn btn-primary px-4">Save Theme</button>

                    @if ($setting?->updated_at)
                        <small class="text-muted">
                            Last changed {{ $setting->updated_at->format('d/m/Y h:i A') }}{{ $setting->updatedBy ? ' by ' . $setting->updatedBy->name : '' }}.
                        </small>
                    @endif
                </div>

                <p class="form-text mt-3 mb-0">
                    Themes only change decoration: the tablet header, a greeting and accent colours.
                    Sign In, Sign Out, Going Out and Returning keep their usual colours, and the
                    Emergency Roll Call page always keeps its normal red header.
                    Remember to switch back to Professional after the holiday.
                </p>
            </form>

        </div>
    </div>

</div>

</body>
</html>
