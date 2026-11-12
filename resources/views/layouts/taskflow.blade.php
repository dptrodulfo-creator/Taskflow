<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ auth()->user()?->theme ?? 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | TaskFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #edf2f6;
            --panel: #f9fbfc;
            --shell: #f4f7f9;
            --sidebar-bg: #f6f8fa;
            --card: #ffffff;
            --line: #dde5ec;
            --line-strong: #d4dde6;
            --text: #1f2430;
            --muted: #6f7d8d;
            --muted-soft: #8b97a7;
            --primary: #1d6ef2;
            --primary-soft: #eaf2ff;
            --primary-dark: #1557d8;
            --success: #10b981;
            --success-soft: #e7faf4;
            --warning: #f3b45f;
            --warning-soft: #fff6e8;
            --danger: #f05d5d;
            --danger-soft: #fff1f1;
            --purple: #9c7cf2;
            --purple-soft: #f5f0ff;
            --shadow: 0 18px 36px rgba(31, 36, 48, 0.08);
        }

        * { box-sizing: border-box; }
        html { background: var(--bg); }
        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }
        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }
        .shell { display: flex; min-height: 100vh; background: var(--bg); }
        .sidebar {
            width: 252px; background: rgba(255,255,255,0.52); border-right: 1px solid rgba(221,229,236,0.9);
            padding: 18px 14px 14px; display: flex; flex-direction: column; backdrop-filter: blur(6px);
        }
        .brand { display: flex; align-items: center; gap: 10px; padding: 8px 12px 26px; font-size: 2.1rem; font-weight: 800; letter-spacing: -0.06em; color: #1f2d3d; }
        .brand-mark { width: 22px; height: 22px; border-radius: 7px; background: linear-gradient(135deg, #0a67f4 0%, #16307b 100%); position: relative; box-shadow: 0 12px 20px rgba(32, 102, 233, 0.22); }
        .brand-mark::before { content: ""; position: absolute; inset: 5px; border-radius: 4px; background: rgba(255,255,255,0.25); transform: skew(-12deg); }
        .nav { display: flex; flex-direction: column; gap: 6px; }
        .nav-item { position: relative; display: flex; align-items: center; gap: 10px; width: 100%; padding: 11px 12px; border-radius: 12px; color: #4d5867; font-weight: 600; transition: all .2s ease; }
        .nav-item:hover { background: rgba(29,110,242,0.06); color: var(--primary); }
        .nav-item.active { background: rgba(29,110,242,0.10); color: var(--primary); box-shadow: inset 0 0 0 1px rgba(29,110,242,0.07); }
        .nav-item .nav-icon { width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center; color: inherit; opacity: 0.9; }
        .nav-badge { margin-left: auto; min-width: 22px; height: 22px; padding: 0 7px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: #ef4444; color: #fff; font-size: 0.7rem; font-weight: 700; line-height: 1; }
        .sidebar-header { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
        .sidebar-toggle { width: 36px; height: 36px; border: 1px solid var(--line); border-radius: 10px; background: rgba(255,255,255,0.7); color: var(--text); font-size: 1rem; cursor: pointer; }
        .sidebar.sidebar-collapsed { width: 64px; }
        .sidebar.sidebar-collapsed .brand, .sidebar.sidebar-collapsed .sidebar-content { display: none; }
        .share-feedback { color: var(--muted); font-size: 0.75rem; }
        .nav-item .nav-icon::before { content: ""; width: 12px; height: 12px; display: block; border: 1.7px solid currentColor; border-radius: 4px; box-sizing: border-box; }
        .nav-item:nth-child(2) .nav-icon::before { border-radius: 2px; box-shadow: inset 0 0 0 1px currentColor; }
        .nav-item:nth-child(3) .nav-icon::before, .nav-item:nth-child(4) .nav-icon::before, .nav-item:nth-child(5) .nav-icon::before, .nav-item:nth-child(6) .nav-icon::before, .nav-item:nth-child(7) .nav-icon::before { border-radius: 50%; border-width: 1.5px; }
        .nav-label { margin-top: 18px; padding: 8px 12px 10px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted-soft); }
        .sidebar-footer { margin-top: auto; padding-top: 18px; border-top: 1px solid var(--line); }
        .profile-block { display: flex; align-items: center; gap: 10px; padding: 8px 8px 12px; }
        .profile-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #fed7aa, #fdba74 45%, #f97316); display: grid; place-items: center; color: white; font-weight: 700; font-size: 0.85rem; }
        .profile-meta { display: grid; gap: 2px; }
        .profile-name { font-size: 0.95rem; font-weight: 700; }
        .profile-email { font-size: 0.72rem; color: var(--muted); }
        .logout-btn { width: 100%; border: 0; border-radius: 10px; padding: 10px 12px; background: #eef4ff; color: var(--primary-dark); font-weight: 700; cursor: pointer; }
        .main { flex: 1; padding: 18px 28px 30px; }
        .content-shell { max-width: 1240px; margin: 0 auto; width: 100%; }
        .top-toolbar { display: flex; align-items: center; gap: 16px; padding: 10px 0 4px; }
        .search-box { flex: 1; display: flex; align-items: center; gap: 10px; height: 54px; padding: 0 16px; border: 1px solid var(--line); border-radius: 16px; background: rgba(255,255,255,0.66); box-shadow: 0 6px 12px rgba(31,36,48,0.02); }
        .search-box svg { width: 18px; height: 18px; color: var(--muted); flex-shrink: 0; }
        .search-box input { flex: 1; border: 0; background: transparent; color: var(--text); font-size: 0.98rem; outline: none; }
        .search-box input::placeholder { color: var(--muted-soft); }
        .toolbar-actions { display: flex; align-items: center; gap: 12px; }
        .status-pill { display: inline-flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 999px; background: rgba(255,255,255,0.7); border: 1px solid var(--line); font-weight: 700; color: var(--text); }
        .toolbar-icon { width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--line); background: rgba(255,255,255,0.7); display: grid; place-items: center; color: var(--text); }
        .toolbar-share { display: inline-flex; align-items: center; gap: 8px; padding: 9px 14px; border-radius: 12px; border: 1px solid var(--line); background: rgba(255,255,255,0.7); font-weight: 600; }
        .profile-mini { display: flex; align-items: center; gap: 12px; padding: 8px 10px; border-radius: 14px; background: rgba(255,255,255,0.7); border: 1px solid var(--line); }
        .profile-mini .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #f7d3a0, #f39c54 48%, #e67e22); display: grid; place-items: center; color: #fff; font-weight: 700; }
        .profile-mini .meta { display: grid; line-height: 1.2; }
        .profile-mini .meta strong { font-size: 0.9rem; }
        .profile-mini .meta span { color: var(--muted); font-size: 0.72rem; }
        .dashboard-layout { display: grid; grid-template-columns: minmax(0, 2.25fr) minmax(260px, 0.8fr); gap: 18px; margin-top: 18px; }
        .main-column { display: flex; flex-direction: column; gap: 18px; }
        .welcome-row { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding-top: 6px; }
        .eyebrow { margin: 0; font-size: 0.85rem; color: var(--muted); font-weight: 600; }
        .welcome-copy h1 { margin: 4px 0 0; font-size: clamp(2rem, 2.4vw, 3rem); letter-spacing: -0.07em; line-height: 1.05; }
        .welcome-copy p { margin: 10px 0 0; color: var(--muted); font-size: 1.02rem; }
        .primary-btn { border: 0; border-radius: 12px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; padding: 12px 18px; font-weight: 700; box-shadow: 0 14px 24px rgba(29,110,242,0.2); cursor: pointer; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .stat-card { background: rgba(255,255,255,0.7); border: 1px solid var(--line); border-radius: 18px; padding: 18px 18px 16px; box-shadow: 0 6px 20px rgba(23,33,43,0.02); }
        .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .stat-label { font-size: 0.85rem; color: var(--muted); font-weight: 600; }
        .stat-icon { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; font-size: 1.05rem; }
        .stat-icon.blue { background: var(--primary-soft); color: var(--primary); }
        .stat-icon.yellow { background: var(--warning-soft); color: #d07f1f; }
        .stat-icon.green { background: var(--success-soft); color: var(--success); }
        .stat-icon.purple { background: var(--purple-soft); color: var(--purple); }
        .stat-icon.red { background: var(--danger-soft); color: var(--danger); }
        .stat-value { margin-top: 18px; font-size: 2.1rem; font-weight: 800; letter-spacing: -0.06em; }
        .stat-meta { margin-top: 8px; display: flex; align-items: center; gap: 7px; font-size: 0.79rem; color: var(--muted); }
        .trend-up { color: var(--success); font-weight: 700; }
        .trend-down { color: var(--danger); font-weight: 700; }
        .analytics-row { display: grid; grid-template-columns: 1fr 1.2fr; gap: 18px; }
        .panel { background: rgba(255,255,255,0.7); border: 1px solid var(--line); border-radius: 18px; padding: 18px 18px 16px; box-shadow: 0 6px 20px rgba(23,33,43,0.02); }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .panel-head h3 { margin: 0; font-size: 1.05rem; letter-spacing: -0.03em; }
        .mini-link { font-size: 0.8rem; color: var(--muted); font-weight: 600; }
        .donut-wrap { display: grid; grid-template-columns: 160px 1fr; align-items: center; gap: 18px; margin-top: 8px; }
        .donut-chart { width: 160px; height: 160px; border-radius: 50%; position: relative; display: grid; place-items: center; margin: 0 auto; }
        .donut-chart::before { content: ""; width: 100px; height: 100px; background: rgba(255,255,255,0.95); border-radius: 50%; position: absolute; inset: 30px; box-shadow: inset 0 0 0 1px rgba(221,229,236,0.7); }
        .donut-value { position: relative; z-index: 1; display: grid; place-items: center; text-align: center; }
        .donut-value strong { font-size: 1.9rem; letter-spacing: -0.06em; }
        .donut-value span { color: var(--muted); font-size: 0.8rem; font-weight: 600; }
        .legend-list { display: grid; gap: 12px; padding: 0; margin: 0; list-style: none; }
        .legend-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; font-size: 0.92rem; color: var(--muted); }
        .legend-row .label { display: inline-flex; align-items: center; gap: 8px; font-weight: 600; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .legend-dot.blue { background: var(--primary); }
        .legend-dot.green { background: var(--success); }
        .legend-dot.gold { background: var(--warning); }
        .legend-dot.red { background: var(--danger); }
        .chart-panel { overflow: hidden; }
        .mini-chart { margin-top: 8px; width: 100%; height: 170px; display: block; }
        .chart-legend { display: flex; align-items: center; gap: 8px; margin-top: 8px; color: var(--muted); font-size: 0.82rem; font-weight: 600; }
        .chart-legend .dot { width: 9px; height: 9px; background: var(--primary); border-radius: 50%; }
        .lower-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.85fr); gap: 18px; }
        .task-list-panel { padding-bottom: 10px; }
        .task-tabs { display: flex; gap: 8px; margin: 12px 0 18px; flex-wrap: wrap; }
        .task-tab { border: 1px solid transparent; background: #edf3f7; color: var(--muted); border-radius: 10px; padding: 7px 12px; font-size: 0.8rem; font-weight: 700; }
        .task-tab.active { background: rgba(29,110,242,0.10); color: var(--primary); border-color: rgba(29,110,242,0.08); }
        .task-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
        .task-row { display: grid; grid-template-columns: 22px minmax(0,1.5fr) auto auto; align-items: center; gap: 12px; padding: 10px 8px; border-radius: 12px; border: 1px solid transparent; }
        .task-row:hover { background: rgba(29,110,242,0.03); border-color: rgba(29,110,242,0.06); }
        .task-check { width: 18px; height: 18px; border-radius: 6px; border: 1.6px solid #cad6e1; background: white; display: grid; place-items: center; color: white; }
        .task-row.is-done .task-check { background: var(--success); border-color: var(--success); }
        .task-title { font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .task-row.is-done .task-title { text-decoration: line-through; color: var(--muted); }
        .priority-label { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: 6px 10px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.01em; }
        .priority-label.low { background: #edfbf4; color: #1d9d63; }
        .priority-label.medium { background: #fff6e8; color: #c98521; }
        .priority-label.high { background: #fff1f0; color: #d96660; }
        .priority-label .dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
        .task-date { color: var(--muted); font-size: 0.82rem; font-weight: 600; }
        .calendar-panel { padding-bottom: 14px; }
        .calendar-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .calendar-month { font-size: 1.35rem; font-weight: 700; letter-spacing: -0.05em; margin: 6px 0 12px; }
        .calendar-navs { display: flex; align-items: center; gap: 8px; }
        .calendar-navs button { width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--line); background: rgba(255,255,255,0.7); color: var(--text); cursor: pointer; font-size: 1rem; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 7px; margin-top: 10px; }
        .calendar-weekday, .calendar-day { text-align: center; border-radius: 10px; min-height: 36px; display: grid; place-items: center; }
        .calendar-weekday { font-size: 0.72rem; color: var(--muted); font-weight: 700; padding: 5px 0; }
        .calendar-day { position: relative; font-size: 0.8rem; color: var(--text); background: rgba(248,250,252,0.8); border: 1px solid transparent; min-height: 38px; font-weight: 600; }
        .calendar-day.outside-month { color: #aab5c0; background: rgba(249,251,252,0.35); }
        .calendar-day.today { border-color: rgba(29,110,242,0.15); background: var(--primary-soft); color: var(--primary-dark); }
        .calendar-day.has-tasks::after { content: ""; position: absolute; width: 6px; height: 6px; border-radius: 50%; background: var(--primary); bottom: 6px; left: 50%; transform: translateX(-50%); }
        .right-rail { display: flex; flex-direction: column; gap: 18px; }
        .quick-action-list { display: grid; gap: 12px; margin-top: 8px; }
        .quick-action { width: 100%; border: 0; border-radius: 12px; background: rgba(237,243,247,0.8); color: var(--text); display: flex; align-items: center; gap: 12px; padding: 12px 14px; text-align: left; cursor: pointer; font-weight: 700; }
        .quick-action.blue { background: rgba(29,110,242,0.10); color: var(--primary-dark); }
        .quick-action.purple { background: rgba(156,124,242,0.10); color: #6d57d1; }
        .quick-action.green { background: rgba(16,185,129,0.10); color: #0c8d61; }
        .quick-action-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; font-size: 1.1rem; background: rgba(255,255,255,0.7); }
        .quick-action-copy { display: grid; gap: 2px; flex: 1; }
        .quick-action-copy strong { font-size: 0.98rem; }
        .quick-action-copy span { font-size: 0.72rem; color: var(--muted); font-weight: 600; }
        .arrow { font-size: 1.1rem; color: inherit; }
        .deadline-list { list-style: none; padding: 0; margin: 14px 0 0; display: grid; gap: 12px; }
        .deadline-item { display: grid; grid-template-columns: 10px 1fr auto; align-items: center; gap: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--line); }
        .deadline-item:last-child { padding-bottom: 0; border-bottom: 0; }
        .deadline-marker { width: 8px; height: 8px; border-radius: 50%; background: var(--danger); }
        .deadline-item:nth-child(2) .deadline-marker { background: var(--warning); }
        .deadline-item:nth-child(3) .deadline-marker { background: var(--primary); }
        .deadline-item:nth-child(4) .deadline-marker { background: var(--success); }
        .deadline-meta { display: grid; gap: 4px; }
        .deadline-meta strong { font-size: 0.82rem; font-weight: 700; }
        .deadline-meta span { font-size: 0.72rem; color: var(--muted); font-weight: 600; }
        .deadline-tag { padding: 4px 8px; border-radius: 999px; font-size: 0.68rem; font-weight: 700; color: white; }
        .deadline-tag.high { background: var(--danger); }
        .deadline-tag.medium { background: var(--warning); }
        .deadline-tag.low { background: var(--success); }
        .motivation-card { margin-top: 6px; background: linear-gradient(180deg, #ecf4ff 0%, #f2f8ff 100%); border: 1px solid rgba(29,110,242,0.08); border-radius: 18px; padding: 18px 14px 12px; text-align: center; }
        .rocket { font-size: 2.2rem; margin-bottom: 8px; display: block; }
        .motivation-card h4 { margin: 0; font-size: 1.05rem; letter-spacing: -0.04em; }
        .progress-line { height: 9px; background: rgba(29,110,242,0.12); border-radius: 999px; overflow: hidden; margin-top: 12px; }
        .progress-line span { display: block; height: 100%; width: 54%; border-radius: inherit; background: linear-gradient(90deg, var(--primary) 0%, #69a7ff 100%); }
        @media (max-width: 1100px) { .dashboard-layout { grid-template-columns: 1fr; } .right-rail { order: -1; } }
        @media (max-width: 960px) { .shell { display: block; } .sidebar { width: 100%; border-right: 0; border-bottom: 1px solid var(--line); } .sidebar.sidebar-collapsed { width: 100%; } .nav { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); } .main { padding: 18px 18px 24px; } .stats-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } .analytics-row, .lower-grid { grid-template-columns: 1fr; } }
        @media (max-width: 620px) { .toolbar-actions { display: flex; gap: 8px; } .toolbar-actions .status-pill, .toolbar-actions .profile-mini { display: none; } .toolbar-share { padding: 9px; } .welcome-row { flex-direction: column; align-items: flex-start; } .stats-grid { grid-template-columns: 1fr; } .task-row { grid-template-columns: 22px 1fr; } .task-row .priority-label, .task-row .task-date { grid-column: 2; } }
    .primary-btn,
    .quick-action,
    .task-tabs a {
        text-decoration: none;
    }

    .primary-btn,
    .task-tabs a {
        display: inline-flex;
        align-items: center;
    }

    .calendar-navs a {
        display: inline-flex;
        width: 28px;
        height: 28px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: rgba(255,255,255,0.7);
        color: var(--text);
        font-size: 1rem;
        text-decoration: none;
    }

    .task-notification {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 14px 18px;
        border: 1px solid #a7f3d0;
        border-radius: 14px;
        background: #ecfdf5;
        color: #047857;
    }

    .task-notification button {
        border: 0;
        background: transparent;
        color: inherit;
        cursor: pointer;
        font-weight: 600;
    }

    .global-search-dialog {
        width: min(92vw, 640px);
        max-width: none;
        padding: 0;
        border: 0;
        border-radius: 16px;
        background: transparent;
    }

    .global-search-dialog::backdrop {
        background: rgba(15, 23, 42, 0.48);
        backdrop-filter: blur(4px);
    }

    .global-search-dialog form {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: var(--card);
        box-shadow: var(--shadow);
    }

    .global-search-dialog .search-box {
        flex: 1;
    }

    .global-search-dialog [data-close-global-search] {
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 10px 12px;
        background: var(--card);
        color: var(--text);
        cursor: pointer;
    }

    html[data-theme="dark"] {
        color-scheme: dark;
        --bg: #0b1220;
        --panel: #111827;
        --shell: #0f172a;
        --sidebar-bg: #111827;
        --card: #1f2937;
        --line: #334155;
        --line-strong: #475569;
        --text: #e5e7eb;
        --muted: #9ca3af;
        --muted-soft: #94a3b8;
        --primary: #60a5fa;
        --primary-soft: #1e3a8a;
        --primary-dark: #3b82f6;
        --success-soft: #064e3b;
        --warning-soft: #422006;
        --danger-soft: #450a0a;
        --purple-soft: #2e1065;
        --shadow: 0 18px 36px rgba(0, 0, 0, 0.3);
    }

    html[data-theme="dark"] .sidebar {
        background: var(--sidebar-bg);
        border-right-color: var(--line);
    }

    html[data-theme="dark"] .nav-item {
        color: var(--muted);
    }

    html[data-theme="dark"] .nav-item.active {
        background: var(--primary-soft);
        color: var(--primary);
    }

    html[data-theme="dark"] .panel,
    html[data-theme="dark"] .stat-card,
    html[data-theme="dark"] .search-box,
    html[data-theme="dark"] .status-pill,
    html[data-theme="dark"] .toolbar-icon,
    html[data-theme="dark"] .toolbar-share,
    html[data-theme="dark"] .quick-action {
        border-color: var(--line);
        background: var(--card);
        color: var(--text);
    }

    html[data-theme="dark"] .task-tab,
    html[data-theme="dark"] .calendar-day,
    html[data-theme="dark"] .calendar-navs button,
    html[data-theme="dark"] .calendar-navs a {
        border-color: var(--line);
        background: var(--card);
        color: var(--text);
    }

    html[data-theme="dark"] .task-tab.active {
        background: var(--primary-soft);
        color: var(--primary);
    }

    html[data-theme="dark"] .task-check {
        border-color: var(--line-strong);
        background: var(--card);
    }

    html[data-theme="dark"] .priority-label.low {
        background: #064e3b;
        color: #6ee7b7;
    }

    html[data-theme="dark"] .priority-label.medium {
        background: #422006;
        color: #fcd34d;
    }

    html[data-theme="dark"] .priority-label.high {
        background: #450a0a;
        color: #fca5a5;
    }

    html[data-theme="dark"] .calendar-day.outside-month {
        background: rgba(15, 23, 42, 0.35);
        color: var(--muted-soft);
    }

    html[data-theme="dark"] .task-notification {
        border-color: #065f46;
        background: #052e23;
        color: #a7f3d0;
    }

    html[data-theme="dark"] .global-search-dialog form {
        border-color: var(--line);
        background: var(--card);
    }
    </style>
    @vite(['resources/js/app.js'])
</head>
<body>
    <dialog id="global-search-dialog" class="global-search-dialog" aria-label="Global search">
        <form action="{{ route('tasks.index') }}" method="GET">
            <label class="search-box" aria-label="Search tasks">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 16L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <input type="search" name="search" value="" placeholder="Search tasks..." aria-keyshortcuts="Control+K Meta+K" />
            </label>
            <button type="button" data-close-global-search>Close</button>
        </form>
    </dialog>
    <div class="shell">
        <aside class="sidebar">
            <div class="sidebar-header">
                <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-controls="sidebar-content" aria-expanded="true" aria-label="Close navigation menu">☰</button>
                <div class="brand" style="padding: 0; font-size: 1.7rem;">
                    <span class="brand-mark" aria-hidden="true"></span>
                    <span>TaskFlow</span>
                </div>
                <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-controls="sidebar-content" aria-expanded="true" aria-label="Collapse sidebar">✕</button>
            </div>

            <div class="sidebar-content" id="sidebar-content">
                <nav class="nav" aria-label="Main navigation">
                    <a href="{{ route('dashboard') }}" class="nav-item active"><span class="nav-icon" aria-hidden="true"></span><span>Dashboard</span></a>
                    <a href="{{ route('tasks.index') }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>My Tasks</span></a>
                    <a href="{{ route('tasks.index', ['due_date' => today()->toDateString()]) }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Today</span></a>
                    <a href="{{ route('tasks.index', ['due_after' => today()->toDateString()]) }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Upcoming</span></a>
                    <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Completed</span></a>
                    <a href="{{ route('notifications.index') }}" class="nav-item" data-tooltip="Notifications">
                        <span class="nav-icon" aria-hidden="true"></span>
                        <span>Notifications</span>
                        @if ($unreadNotificationCount > 0)
                            <span class="nav-badge" aria-label="{{ $unreadNotificationCount }} unread notifications">{{ $unreadNotificationCount }}</span>
                        @endif
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Admin Console</span></a>
                    @endif
                </nav>

                <div class="nav-label">Manage</div>
                <nav class="nav" aria-label="Management navigation">
                    <a href="{{ route('dashboard') }}#calendar" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Calendar</span></a>
                    <a href="{{ route('dashboard') }}#analytics" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Task analytics</span></a>
                    <a href="{{ route('tasks.create') }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>New Task</span></a>
                    <a href="{{ route('profile.edit') }}" class="nav-item"><span class="nav-icon" aria-hidden="true"></span><span>Settings</span></a>
                </nav>

                <div class="sidebar-footer">
                    <div class="profile-block">
                        <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="profile-meta">
                            <div class="profile-name">{{ auth()->user()->name }}</div>
                            <div class="profile-email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">Sign out</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="main">
            <div class="content-shell">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
