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
            background-size: 120px 120px, cover, cover;
            background-repeat: repeat, no-repeat, no-repeat;
            background-position: 0 0, center, center;
        }

        .custom-panel {
            margin-top: 24px;
            padding: 20px;
            border: 1px solid var(--gf-border);
            border-radius: 14px;
            background: var(--gf-subtle);
        }

        .custom-panel h5 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .custom-photo-current {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
        }

        .custom-photo-current img {
            width: 160px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--gf-border);
        }

        .form-control-color {
            width: 64px;
            padding: 4px;
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
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('theme.update') }}" enctype="multipart/form-data" id="themeForm">
                @csrf
                @method('PUT')

                <div class="theme-grid" role="radiogroup" aria-label="Theme">
                    @foreach ($themes as $key => $theme)
                        <label class="theme-option">
                            <input type="radio" name="theme" value="{{ $key }}" @checked(old('theme', $currentKey) === $key)>

                            <div class="theme-preview" @if ($key === 'custom') id="customPreview" @endif
                                 style="background-image: {{ $theme['patternUrl'] ? $theme['patternUrl'] . ', ' : '' }}{{ $theme['hero'] }};">
                                @if ($theme['emoji'])
                                    <span class="theme-preview-emoji" aria-hidden="true">{{ $theme['emoji'] }}</span>
                                @endif
                                <span class="theme-preview-greeting" @if ($key === 'custom') id="customPreviewGreeting" @endif>{{ $theme['greeting'] ?? 'Goodman Fielder Attendance System' }}</span>
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

                <div class="custom-panel" id="customPanel">
                    <h5>Custom theme</h5>
                    <p class="text-muted small mb-3">
                        Your own photo, greeting and colour. Changing anything here selects the Custom theme;
                        click Save Theme to show it.
                    </p>

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label for="custom_greeting" class="form-label">Greeting text</label>
                            <input type="text" id="custom_greeting" name="custom_greeting" class="form-control"
                                   maxlength="80" value="{{ old('custom_greeting', $custom['greeting']) }}"
                                   placeholder="For example: Congratulations on 100 years, Goodman Fielder Fiji!">
                            <div class="form-text">Shown on the tablet welcome screen and at the top of admin pages. Up to 80 characters; emojis are fine.</div>
                        </div>

                        <div class="col-sm-4 col-lg-2">
                            <label for="custom_color" class="form-label">Colour</label>
                            <input type="color" id="custom_color" name="custom_color" class="form-control form-control-color"
                                   value="{{ old('custom_color', $custom['color']) }}">
                            <div class="form-text">Used for accents, and for the header when there is no photo.</div>
                        </div>

                        <div class="col-sm-8 col-lg-4">
                            <label for="custom_photo" class="form-label">Header photo</label>
                            <input type="file" id="custom_photo" name="custom_photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text" id="customPhotoHelp">JPG, PNG or WEBP. A wide landscape photo works best. Large photos are made smaller automatically.</div>

                            @if ($custom['photo'])
                                <div class="custom-photo-current">
                                    <img src="{{ route('theme.photo', $custom['photo']) }}" alt="Current header photo">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo" value="1">
                                        <label class="form-check-label" for="remove_photo">Remove photo</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('themeForm');
        const customRadio = form.querySelector('input[name="theme"][value="custom"]');
        const preview = document.getElementById('customPreview');
        const previewGreeting = document.getElementById('customPreviewGreeting');
        const greeting = document.getElementById('custom_greeting');
        const color = document.getElementById('custom_color');
        const photo = document.getElementById('custom_photo');
        const photoHelp = document.getElementById('customPhotoHelp');
        const remove = document.getElementById('remove_photo');
        const submit = form.querySelector('button[type="submit"]');

        const WASH = 'linear-gradient(180deg, rgba(10, 18, 35, .45) 0%, rgba(10, 18, 35, .72) 100%)';
        const savedPhoto = @json($custom['photo'] ? route('theme.photo', $custom['photo']) : null);
        let newPhoto = null;
        let resizing = null;

        function selectCustom() {
            customRadio.checked = true;
        }

        function draw() {
            const url = newPhoto || (savedPhoto && !(remove && remove.checked) ? savedPhoto : null);
            preview.style.backgroundImage = url
                ? WASH + ', url("' + url + '")'
                : 'linear-gradient(135deg, #0f1f3d 0%, ' + color.value + ' 100%)';
            previewGreeting.textContent = greeting.value.trim() || @json(\App\Support\FestiveTheme::CUSTOM_DEFAULTS['greeting']);
        }

        // Shrink big phone photos in the browser so they fit the 2 MB upload limit.
        function shrink(file) {
            return new Promise(function (resolve) {
                if (!window.DataTransfer || !window.createImageBitmap) return resolve(file);

                createImageBitmap(file).then(function (bitmap) {
                    const scale = Math.min(1, 1920 / bitmap.width, 1280 / bitmap.height);
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(bitmap.width * scale);
                    canvas.height = Math.round(bitmap.height * scale);
                    const context = canvas.getContext('2d');
                    context.fillStyle = '#ffffff';
                    context.fillRect(0, 0, canvas.width, canvas.height);
                    context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob(function (blob) {
                        resolve(blob ? new File([blob], 'header-photo.jpg', { type: 'image/jpeg' }) : file);
                    }, 'image/jpeg', 0.85);
                }).catch(function () {
                    resolve(file);
                });
            });
        }

        greeting.addEventListener('input', function () { selectCustom(); draw(); });
        color.addEventListener('input', function () { selectCustom(); draw(); });

        if (remove) {
            remove.addEventListener('change', function () { selectCustom(); draw(); });
        }

        photo.addEventListener('change', function () {
            const file = photo.files[0];
            if (!file) return;

            selectCustom();
            photoHelp.textContent = 'Preparing photo...';

            resizing = shrink(file).then(function (ready) {
                if (ready !== file) {
                    const transfer = new DataTransfer();
                    transfer.items.add(ready);
                    photo.files = transfer.files;
                }

                if (newPhoto) URL.revokeObjectURL(newPhoto);
                newPhoto = URL.createObjectURL(ready);
                if (remove) remove.checked = false;

                photoHelp.textContent = 'Ready: ' + Math.max(1, Math.round(ready.size / 1024)) + ' KB. Click Save Theme to use it.';
                draw();
                resizing = null;
            });
        });

        form.addEventListener('submit', function (event) {
            if (!resizing) {
                submit.disabled = true;
                submit.textContent = 'Saving...';
                return;
            }

            event.preventDefault();
            resizing.then(function () { form.requestSubmit(); });
        });
    });
</script>

</body>
</html>
