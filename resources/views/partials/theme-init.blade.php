{{--
    Light / dark mode. Include as early as possible inside <head> on every page:
        @include('partials.theme-init')

    The choice is saved on this device (localStorage "gf-theme"). Until someone
    picks one, the page follows the device's own light / dark setting.
    It runs before the page draws, so there is no white flash in dark mode.
    Any element with data-theme-toggle switches the theme when clicked
    (see partials/theme-toggle). Plain inline code on purpose: it must also
    work on the offline emergency page, which has no build assets.
--}}
<meta name="color-scheme" content="light dark">
<script>
    (function () {
        var KEY = 'gf-theme';
        var root = document.documentElement;
        var query = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

        function saved() {
            try {
                var value = localStorage.getItem(KEY);
                return value === 'dark' || value === 'light' ? value : null;
            } catch (e) {
                return null;
            }
        }

        function apply(theme) {
            root.setAttribute('data-theme', theme);
            root.setAttribute('data-coreui-theme', theme);
            root.setAttribute('data-bs-theme', theme);
            root.style.colorScheme = theme;

            var label = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
            var buttons = document.querySelectorAll('[data-theme-toggle]');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].setAttribute('aria-label', label);
                buttons[i].setAttribute('title', label);
                buttons[i].setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            }
        }

        apply(saved() || (query && query.matches ? 'dark' : 'light'));

        if (query && query.addEventListener) {
            query.addEventListener('change', function (event) {
                if (!saved()) apply(event.matches ? 'dark' : 'light');
            });
        }

        document.addEventListener('click', function (event) {
            var button = event.target.closest ? event.target.closest('[data-theme-toggle]') : null;
            if (!button) return;

            event.preventDefault();
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            try { localStorage.setItem(KEY, next); } catch (e) {}
            apply(next);
        });

        // Buttons further down the page did not exist yet when apply() first ran.
        document.addEventListener('DOMContentLoaded', function () {
            apply(root.getAttribute('data-theme'));
        });
    })();
</script>
@include('partials.festive-theme')
<style>
    [data-theme="dark"] .theme-icon-moon,
    :root:not([data-theme="dark"]) .theme-icon-sun {
        display: none;
    }
</style>
