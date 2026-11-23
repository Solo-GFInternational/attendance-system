{{--
    Shared kiosk theme (welcome page, Employee Attendance, Visitor Register,
    Emergency Roll Call). Include inside <head>: @include('partials.kiosk-theme')
    Plain CSS on purpose: no build step, and it works offline on the
    emergency page because it is part of the cached HTML.
--}}
<style>

    :root {
        --navy: #0f1f3d;
        --navy-2: #16294f;
        --bg: #f3f5f9;
        --surface: #ffffff;
        --border: #e2e7ef;
        --border-strong: #cfd6e1;
        --text: #111827;
        --muted: #64748b;

        --blue: #1f5fd6;
        --blue-dark: #194fb3;
        --blue-soft: #eaf1fd;
        --green: #13803f;
        --green-dark: #0f6a34;
        --green-soft: #e7f6ed;
        --amber: #b45309;
        --amber-soft: #fef6e7;
        --red: #c62f3b;
        --red-dark: #a3222d;
        --red-soft: #fdecee;
        --violet: #6d3fc0;
        --violet-soft: #f1ecfb;

        --radius: 16px;
        --radius-sm: 10px;
        --shadow: 0 1px 2px rgba(16, 24, 40, .06), 0 8px 24px rgba(16, 24, 40, .06);
        --shadow-hover: 0 2px 4px rgba(16, 24, 40, .06), 0 16px 36px rgba(16, 24, 40, .10);
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: var(--bg);
        color: var(--text);
        font-family: "Segoe UI", system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    svg {
        display: block;
    }


    /* =====================================================
       HERO BAND
    ===================================================== */

    .k-hero {
        background:
            radial-gradient(1200px 400px at 85% -10%, rgba(31, 95, 214, .35), transparent 60%),
            linear-gradient(180deg, var(--navy) 0%, var(--navy-2) 100%);
        color: #ffffff;
        padding: 22px 32px 96px;
    }

    .k-hero.is-welcome {
        padding-bottom: 120px;
    }

    .k-hero.is-emergency {
        background:
            radial-gradient(1200px 400px at 85% -10%, rgba(198, 47, 59, .45), transparent 60%),
            linear-gradient(180deg, var(--navy) 0%, var(--navy-2) 100%);
    }

    .k-topbar,
    .k-hero-body {
        max-width: 1120px;
        margin: 0 auto;
    }

    .k-wide .k-topbar,
    .k-wide .k-hero-body,
    .k-wide .k-main {
        max-width: 1440px;
    }

    .k-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .k-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
        text-decoration: none;
    }

    .k-brand-logo {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #ffffff;
        padding: 3px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .k-brand-text strong {
        display: block;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: .01em;
    }

    .k-brand-text span {
        display: block;
        font-size: 13px;
        color: #b6c2d9;
    }

    .k-now {
        text-align: right;
    }

    .k-now-time {
        font-size: 26px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        letter-spacing: .01em;
        line-height: 1.1;
    }

    .k-now-date {
        font-size: 13px;
        color: #b6c2d9;
        margin-top: 2px;
    }

    /* Welcome (centred) */
    .k-hero.is-welcome .k-hero-body {
        margin-top: 56px;
        text-align: center;
    }

    /* Sub pages (title row) */
    .k-hero-body {
        margin-top: 40px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .k-hero.is-welcome .k-hero-body {
        display: block;
    }

    .k-title-group {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .k-title-icon {
        display: grid;
        place-items: center;
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .18);
        color: #ffffff;
        flex-shrink: 0;
    }

    .k-title-icon svg {
        width: 28px;
        height: 28px;
    }

    .k-hero h1 {
        margin: 0;
        font-size: 34px;
        font-weight: 700;
        letter-spacing: -.02em;
        line-height: 1.15;
    }

    .k-hero.is-welcome h1 {
        font-size: 40px;
    }

    .k-hero-sub {
        margin: 6px 0 0;
        font-size: 16px;
        color: #c3cee3;
    }

    .k-hero.is-welcome .k-hero-sub {
        margin-top: 10px;
        font-size: 18px;
    }

    .k-hero-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .k-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .18);
        color: #e5ecf8;
        font-size: 13px;
        font-weight: 600;
    }

    .k-chip svg {
        width: 15px;
        height: 15px;
    }

    .k-hero.is-welcome .k-chip {
        margin-bottom: 18px;
    }

    .k-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .22);
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s ease;
    }

    .k-back:hover {
        background: rgba(255, 255, 255, .18);
    }

    .k-back svg {
        width: 18px;
        height: 18px;
    }


    /* =====================================================
       MAIN AREA (overlaps the hero band)
    ===================================================== */

    .k-main {
        width: 100%;
        max-width: 1120px;
        margin: -64px auto 0;
        padding: 0 32px 32px;
        flex: 1;
    }

    .k-hero.is-welcome + .k-main {
        margin-top: -80px;
    }


    /* =====================================================
       CARDS
    ===================================================== */

    .k-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 28px;
    }

    .k-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
    }

    .k-card-head h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .k-card-head p {
        margin: 3px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    .k-card-icon {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--blue-soft);
        color: var(--blue);
        flex-shrink: 0;
    }

    .k-card-icon svg {
        width: 22px;
        height: 22px;
    }

    .k-card-icon.green  { background: var(--green-soft);  color: var(--green); }
    .k-card-icon.red    { background: var(--red-soft);    color: var(--red); }
    .k-card-icon.violet { background: var(--violet-soft); color: var(--violet); }


    /* =====================================================
       FORMS
    ===================================================== */

    .k-field {
        margin-bottom: 18px;
    }

    .k-field label,
    .k-label {
        display: block;
        margin-bottom: 7px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .k-field .req {
        color: var(--red);
    }

    .k-input {
        width: 100%;
        min-height: 52px;
        padding: 13px 15px;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        background: #ffffff;
        color: var(--text);
        font-family: inherit;
        font-size: 16px;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    textarea.k-input {
        min-height: 110px;
        resize: vertical;
    }

    .k-input::placeholder {
        color: #9aa5b5;
    }

    .k-input:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 4px rgba(31, 95, 214, .14);
    }


    /* =====================================================
       BUTTONS
    ===================================================== */

    .k-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 52px;
        padding: 0 22px;
        border: 1px solid transparent;
        border-radius: 12px;
        background: var(--blue);
        color: #ffffff;
        font-family: inherit;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: .01em;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s ease, box-shadow .15s ease, opacity .15s ease;
    }

    .k-btn svg {
        width: 19px;
        height: 19px;
    }

    .k-btn:hover:not(:disabled) {
        background: var(--blue-dark);
    }

    .k-btn:focus-visible {
        outline: 3px solid rgba(31, 95, 214, .45);
        outline-offset: 2px;
    }

    .k-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .k-btn.block   { width: 100%; }
    .k-btn.green   { background: var(--green); }
    .k-btn.green:hover:not(:disabled)  { background: var(--green-dark); }
    .k-btn.red     { background: var(--red); }
    .k-btn.red:hover:not(:disabled)    { background: var(--red-dark); }
    .k-btn.ghost   { background: #ffffff; color: #374151; border-color: var(--border-strong); }
    .k-btn.ghost:hover:not(:disabled)  { background: #f3f5f9; }
    .k-btn.sm      { min-height: 42px; font-size: 14px; padding: 0 16px; border-radius: 10px; }


    /* =====================================================
       ALERTS / NOTICES
    ===================================================== */

    .k-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.45;
    }

    .k-alert svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .k-alert.success { background: var(--green-soft); border-color: #b7e2c6; color: #0f5a2c; }
    .k-alert.error   { background: var(--red-soft);   border-color: #f3c6cb; color: #8f1d27; }
    .k-alert.info    { background: var(--blue-soft);  border-color: #c9d9f7; color: #1a4597; }
    .k-alert.warning { background: var(--amber-soft); border-color: #f6dca6; color: #7c4a03; }


    /* =====================================================
       FOOTER
    ===================================================== */

    .k-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 18px 20px 26px;
        color: var(--muted);
        font-size: 14px;
        text-align: center;
    }

    .k-footer svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }


    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 860px) {

        .k-hero {
            padding: 18px 20px 84px;
        }

        .k-hero.is-welcome {
            padding-bottom: 100px;
        }

        .k-hero-body {
            margin-top: 28px;
        }

        .k-hero.is-welcome .k-hero-body {
            margin-top: 40px;
        }

        .k-hero h1 {
            font-size: 27px;
        }

        .k-hero.is-welcome h1 {
            font-size: 32px;
        }

        .k-title-icon {
            width: 48px;
            height: 48px;
        }

        .k-main {
            padding: 0 20px 24px;
        }

        .k-card {
            padding: 22px;
        }
    }

    @media (max-width: 520px) {

        .k-brand-text span {
            display: none;
        }

        .k-now-time {
            font-size: 20px;
        }

        .k-now-date {
            font-size: 12px;
        }

        .k-hero.is-welcome h1 {
            font-size: 27px;
        }

        .k-title-icon {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
        }
    }

</style>
