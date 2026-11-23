{{-- Shows the tablet link while the site code is typed. --}}
<div class="form-text">
    Letters, numbers and dashes only. The code becomes the tablet link:
    <strong class="text-break" id="siteLinkPreview"></strong>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('input[name="code"]');
        const preview = document.getElementById('siteLinkPreview');
        const base = @json(url('/attendance'));

        function update() {
            preview.textContent = base + '/' + (input.value.trim().toUpperCase() || 'CODE');
        }

        input.addEventListener('input', update);
        update();
    });
</script>
