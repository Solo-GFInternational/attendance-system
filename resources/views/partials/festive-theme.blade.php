{{--
    Festive theme chosen by an admin (Theme page). Included by partials/theme-init,
    so it reaches every admin and tablet page. Writes nothing for Professional.
    Only decorates: the tablet header band, the admin nav accent and greeting
    strip. Action button colours and the Emergency Roll Call header never change.
--}}
@php $festive = \App\Support\FestiveTheme::current(); @endphp
@if ($festive['isFestive'])
<script>document.documentElement.setAttribute('data-festive', @json($festive['key']));</script>
<style>
    :root {
        --festive-hero: {!! $festive['hero'] !!};
        --festive-pattern: {!! $festive['patternUrl'] ?? 'none' !!};
        --festive-accent: {{ $festive['accent'][0] }};
        --festive-soft: {{ $festive['soft'][0] }};
    }

    [data-theme="dark"] {
        --festive-accent: {{ $festive['accent'][1] }};
        --festive-soft: {{ $festive['soft'][1] }};
    }

    /* Tablet header band. Not the emergency page: it stays red for safety. */
    /* Layers: pattern tile, then the band (for Custom with a photo: a dark wash, then the photo). */
    [data-festive] .k-hero:not(.is-emergency) {
        background-image: var(--festive-pattern), var(--festive-hero);
        background-size: 120px 120px, cover, cover;
        background-repeat: repeat, no-repeat, no-repeat;
        background-position: 0 0, center, center;
    }

    [data-festive] .k-chip.k-festive {
        background: rgba(255, 255, 255, .16);
        border-color: rgba(255, 255, 255, .3);
        color: #ffffff;
        font-size: 15px;
        margin-right: 8px;
    }

    /* Admin pages */
    [data-festive] .gf-nav {
        box-shadow: inset 0 3px 0 var(--festive-accent), var(--gf-shadow);
    }

    [data-festive] .gf-nav-link.active {
        background: var(--festive-soft);
        color: var(--festive-accent);
    }

    [data-festive] .gf-festive-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: -10px 0 24px;
        padding: 10px 16px;
        border-radius: 12px;
        background-image: var(--festive-pattern), var(--festive-hero);
        background-size: 120px 120px, cover, cover;
        background-repeat: repeat, no-repeat, no-repeat;
        background-position: 0 0, center, center;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
    }

    [data-festive] .gf-festive-banner a {
        margin-left: auto;
        color: #ffffff;
        font-size: 13px;
        opacity: .85;
    }

    [data-festive] .gf-festive-banner a:hover {
        opacity: 1;
    }
</style>
@endif
