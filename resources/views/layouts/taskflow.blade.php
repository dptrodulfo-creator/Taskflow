<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | TaskFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f5f7f5;
            --white: #ffffff;
            --ink: #18231d;
            --muted: #78847d;
            --line: #e7ece8;
            --green: #287a52;
            --green-soft: #e8f4ec;
            --red: #c94d4d;
            --amber: #b7791f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button, input, textarea, select {
            font: inherit;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 245px 1fr;
        }

        .sidebar {
            background: var(--white);
            border-right: 1px solid var(--line);
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -1px;
            padding: 0 12px 35px;
        }

        .brand span {
            color: var(--green);
        }

        .nav-label {
            font-size: 11px;
            color: #9aa49d;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 12px 10px;
        }

        .nav a {
            display: block;
            padding: 12px 14px;
            border-radius: 10px;
            color: #68756d;
            font-weight: 600;
            margin: 4px 0;
        }

        .nav a:hover,
        .nav a.active {
            background: var(--green-soft);
            color: var(--green);
        }

        .sidebar-bottom {
            margin-top: auto;
            border-top: 1px solid var(--line);
            padding: 18px 10px 0;
            color: var(--muted);
            font-size: 13px;
        }

        .main {
            padding: 35px clamp(20px, 4vw, 58px);
            max-width: 1500px;
            width: 100%;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 30px;
            letter-spacing: -1px;
            margin: 0 0 5px;
        }

        .sub {
            color: var(--muted);
            margin: 0;
            font-size: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
            background: var(--green);
            color: white;
        }

        .btn:hover {
            filter: brightness(.95);
        }

        .btn-light {
            background: white;
            color: var(--ink);
            border: 1px solid var(--line);
        }

        .btn-danger {
            background: #fff0f0;
            color: var(--red);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin: 24px 0;
        }

        .card, .panel {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 15px;
            padding: 20px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            margin-top: 9px;
        }

        .panel {
            margin-top: 20px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .panel h2 {
            font-size: 17px;
            margin: 0;
        }

        .progress {
            height: 9px;
            background: #edf1ee;
            border-radius: 99px;
            overflow: hidden;
            margin: 12px 0;
        }

        .progress span {
            display: block;
            height: 100%;
            background: var(--green);
            border-radius: 99px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th {
            text-align: left;
            color: #929d95;
            font-size: 11px;
            text-transform: uppercase;
            padding: 12px;
            border-bottom: 1px solid var(--line);
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #f0f2f0;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .task-title {
            font-weight: 700;
        }

        .muted {
            color: var(--muted);
        }

        .pill {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
            background: #f0f2f0;
            color: #69756d;
        }

        .pill.high {
            background: #fff0ee;
            color: #c04e43;
        }

        .pill.medium {
            background: #fff6e5;
            color: #a36b13;
        }

        .pill.low {
            background: #eaf4ed;
            color: #367b50;
        }

        .pill.completed {
            background: #e8f4ec;
            color: #287a52;
        }

        .pill.in_progress {
            background: #edf2ff;
            color: #536cb2;
        }

        .filters {
            display: grid;
            grid-template-columns: minmax(160px, 2fr) repeat(5, minmax(110px, 1fr)) auto;
            gap: 10px;
            align-items: end;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 16px;
        }

        label {
            font-size: 13px;
            font-weight: 700;
            color: #536057;
        }

        input, textarea, select {
            width: 100%;
            border: 1px solid var(--line);
            background: white;
            border-radius: 9px;
            padding: 11px 12px;
            color: var(--ink);
            outline: none;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #72a989;
            box-shadow: 0 0 0 3px #e8f4ec;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-card {
            max-width: 760px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .form-actions, .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .alert {
            position: fixed;
            top: 20px;
            right: 22px;
            z-index: 20;
            display: flex;
            width: min(420px, calc(100vw - 32px));
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 13px 15px;
            border-radius: 10px;
            background: #e8f4ec;
            color: var(--green);
            font-size: 14px;
            box-shadow: 0 12px 35px rgba(24, 39, 67, .16);
            transition: opacity .2s ease, transform .2s ease;
        }

        .toast-dismissed {
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px);
        }

        .toast-close {
            display: grid;
            width: 28px;
            height: 28px;
            flex: 0 0 auto;
            place-items: center;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: inherit;
            cursor: pointer;
            font-size: 20px;
        }

        .toast-close:hover {
            background: rgba(40, 117, 210, .08);
        }

        .delete-confirmation {
            width: min(440px, calc(100vw - 32px));
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 16px;
            color: var(--ink);
            box-shadow: 0 20px 70px rgba(24, 39, 67, .22);
        }

        .delete-confirmation::backdrop {
            background: rgba(24, 39, 67, .42);
            backdrop-filter: blur(2px);
        }

        .delete-confirmation h2 {
            margin: 0 0 8px;
            font-size: 18px;
        }

        .delete-confirmation p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .delete-confirmation .actions {
            justify-content: flex-end;
            margin-top: 22px;
        }

        .errors {
            color: var(--red);
            font-size: 12px;
        }

        .empty {
            text-align: center;
            padding: 45px 15px;
            color: var(--muted);
        }

        @media(max-width: 900px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                padding: 14px 18px;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }

            .brand {
                padding: 0 0 12px;
            }

            .nav-label, .sidebar-bottom {
                display: none;
            }

            .nav {
                display: flex;
                gap: 5px;
                overflow-x: auto;
            }

            .nav a {
                white-space: nowrap;
            }

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .filters {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .filters .search {
                grid-column: span 3;
            }
        }

        @media(max-width: 600px) {
            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .filters, .form-grid {
                grid-template-columns: 1fr;
            }

            .filters .search {
                grid-column: auto;
            }

            .cards {
                gap: 10px;
            }

            .card {
                padding: 15px;
            }

            .stat-value {
                font-size: 25px;
            }
        }

        :root {
            --bg: #f4f7fc;
            --ink: #1b2740;
            --muted: #7c879b;
            --line: #e7ebf3;
            --green: #2875d2;
            --green-soft: #edf4ff;
        }

        body {
            background: #f4f7fc;
        }

        .shell {
            grid-template-columns: 258px minmax(0, 1fr);
        }

        .sidebar {
            border-right-color: #e8edf5;
            position: sticky;
            top: 0;
            height: 100vh;
            min-height: 640px;
            padding: 28px 17px 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 10px 38px;
            font-size: 21px;
            letter-spacing: -.7px;
        }

        .brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 12px;
            background: var(--green);
            color: #fff;
        }

        .brand .brand-mark {
            color: #fff;
        }

        .brand-copy > span > span {
            color: var(--green);
        }

        .brand-mark svg {
            width: 21px;
            height: 21px;
        }

        .brand-copy {
            display: grid;
            gap: 1px;
        }

        .brand-copy small {
            color: var(--muted);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .55px;
            text-transform: uppercase;
        }

        .nav {
            display: grid;
            gap: 5px;
        }

        .nav-label {
            padding: 0 12px 9px;
            font-size: 10px;
            letter-spacing: 1.15px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 46px;
            margin: 0;
            padding: 0 13px;
            border-radius: 11px;
            color: #718077;
            font-size: 13px;
            font-weight: 600;
            transition: background .18s ease, color .18s ease, transform .18s ease;
        }

        .nav a:hover {
            background: #f5f8fd;
            color: var(--ink);
            transform: translateX(2px);
        }

        .nav a.active {
            background: var(--green-soft);
            color: var(--green);
        }

        .nav a svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
        }

        .main {
            max-width: 1480px;
            padding: 42px clamp(22px, 4vw, 58px);
        }

        .topbar {
            align-items: center;
            margin-bottom: 25px;
        }

        .eyebrow {
            margin: 0 0 11px;
            color: #8a978e;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .65px;
            text-transform: uppercase;
        }

        h1 {
            margin-bottom: 7px;
            font-size: clamp(25px, 2.4vw, 32px);
            letter-spacing: -1.15px;
        }

        .sub {
            font-size: 13px;
            line-height: 1.6;
        }

        .btn {
            min-height: 43px;
            gap: 8px;
            border-radius: 10px;
            padding: 0 16px;
            box-shadow: 0 5px 12px rgba(40, 117, 210, .16);
            font-size: 13px;
            transition: background .18s ease, transform .18s ease, box-shadow .18s ease;
        }

        .btn:hover {
            background: #2164b5;
            box-shadow: 0 7px 16px rgba(40, 117, 210, .22);
            filter: none;
            transform: translateY(-1px);
        }

        .btn svg {
            width: 17px;
            height: 17px;
        }

        .cards {
            gap: 15px;
            margin: 0 0 18px;
        }

        .card, .panel {
            border-color: #e6ebf3;
            border-radius: 14px;
            box-shadow: 0 3px 14px rgba(30, 48, 36, .025);
        }

        .card {
            padding: 19px 19px 17px;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .card:hover {
            border-color: #d9e5f6;
            box-shadow: 0 9px 22px rgba(30, 48, 36, .055);
            transform: translateY(-2px);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
        }

        .stat-icon {
            display: grid;
            width: 35px;
            height: 35px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 10px;
        }

        .stat-icon svg {
            width: 19px;
            height: 19px;
        }

        .stat-icon-total {
            background: #edf4ff;
            color: #5273b8;
        }

        .stat-icon-pending {
            background: #fff5e7;
            color: #b37a26;
        }

        .stat-icon-progress {
            background: #f0edff;
            color: #7562ba;
        }

        .stat-icon-completed {
            background: #eaf6ee;
            color: #33825a;
        }

        .stat-icon-overdue {
            background: #fff0ee;
            color: #c04e43;
        }

        .overdue-value {
            color: #c04e43;
        }

        .stat-value {
            margin-top: 15px;
            font-size: 29px;
            letter-spacing: -.7px;
        }

        .stat-note {
            margin: 5px 0 0;
            color: #909b93;
            font-size: 11px;
        }

        .panel {
            margin-top: 18px;
            padding: 21px 23px;
        }

        .progress-copy {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .progress-copy h2,
        .panel h2 {
            margin: 0 0 4px;
            font-size: 15px;
            letter-spacing: -.2px;
        }

        .progress-copy strong {
            color: var(--green);
            font-size: 16px;
        }

        .progress {
            height: 7px;
            margin: 16px 0 0;
            background: #edf2ee;
        }

        .progress span {
            background: linear-gradient(90deg, #2875d2, #6ea8f4);
            transition: width .35s ease;
        }

        .panel-head {
            margin-bottom: 13px;
        }

        .panel-head .sub {
            margin: 0;
        }

        .text-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
        }

        .text-link:hover {
            color: #1d5eae;
        }

        .text-link svg {
            width: 15px;
            height: 15px;
        }

        table {
            min-width: 560px;
        }

        th {
            padding: 12px 10px;
            color: #99a39b;
            font-size: 10px;
            letter-spacing: .55px;
        }

        td {
            padding: 14px 10px;
            border-bottom-color: #f0f3f0;
            font-size: 12px;
        }

        .task-title {
            color: #304137;
            font-size: 12px;
            font-weight: 700;
        }

        .priority, .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            border-radius: 99px;
            padding: 6px 9px;
            font-size: 10px;
            font-weight: 700;
        }

        .priority {
            background: #f4f6f4;
            color: #68766d;
        }

        .priority.high {
            background: #fff0ee;
            color: #bd5549;
        }

        .priority.medium {
            background: #fff6e8;
            color: #a87420;
        }

        .priority.low {
            background: #eef6f0;
            color: #43815a;
        }

        .priority-dot, .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .pill {
            background: #f2f4f2;
            color: #68756c;
        }

        .pill.todo {
            background: #fff6e8;
            color: #a87420;
        }

        .pill.in_progress {
            background: #edf2ff;
            color: #536cb2;
        }

        .pill.completed {
            background: #eaf6ee;
            color: #328057;
        }

        .due-date {
            color: #627067;
            white-space: nowrap;
        }

        .due-date.overdue {
            color: #bd5549;
            font-weight: 700;
        }

        .overdue-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 99px;
            padding: 4px 7px;
            background: #fff0ee;
            color: #bd5549;
            font-size: 9px;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-cycle-form {
            display: inline-flex;
            margin: 0;
        }

        .status-cycle {
            border: 0;
            cursor: pointer;
            font-family: inherit;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .status-cycle:hover {
            box-shadow: 0 0 0 2px rgba(40, 117, 210, .15);
            transform: translateY(-1px);
        }

        .status-cycle:focus-visible {
            outline: 2px solid #2875d2;
            outline-offset: 2px;
        }

        .empty {
            display: grid;
            justify-items: center;
            gap: 8px;
            padding: 34px 15px 26px;
        }

        .empty-icon {
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            border-radius: 15px;
            background: #edf7f0;
            color: var(--green);
        }

        .empty-icon svg {
            width: 24px;
            height: 24px;
        }

        .empty h3 {
            margin: 4px 0 0;
            font-size: 14px;
        }

        .empty p {
            margin: 0 0 9px;
            color: var(--muted);
            font-size: 12px;
        }

        .sidebar-bottom {
            display: flex;
            flex-direction: column;
            gap: 13px;
            margin-top: auto;
            padding: 17px 2px 0;
        }

        .account-summary {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .account-avatar {
            display: grid;
            width: 35px;
            height: 35px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 50%;
            background: #e9f4ec;
            color: var(--green);
            font-size: 12px;
            font-weight: 800;
        }

        .account-details {
            display: grid;
            min-width: 0;
            gap: 3px;
        }

        .account-details strong {
            overflow: hidden;
            color: var(--ink);
            font-size: 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-details span {
            overflow: hidden;
            color: var(--muted);
            font-size: 10px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-bottom form {
            width: 100%;
        }

        .sidebar-bottom .btn-light {
            min-height: 36px;
            box-shadow: none;
            font-size: 11px;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 23px;
        }

        .dashboard-header .eyebrow {
            margin-bottom: 8px;
            color: #8491a7;
            text-transform: none;
        }

        .dashboard-header h1 {
            margin-bottom: 5px;
            font-size: clamp(24px, 2.5vw, 31px);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-avatar {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 3px solid #fff;
            border-radius: 50%;
            background: #e8f0fd;
            box-shadow: 0 0 0 1px #e3eaf5;
            color: #3474c3;
            font-size: 13px;
            font-weight: 800;
        }

        .quick-add {
            position: relative;
        }

        .quick-add > summary {
            list-style: none;
            user-select: none;
        }

        .quick-add > summary::-webkit-details-marker {
            display: none;
        }

        .quick-add-panel {
            position: absolute;
            z-index: 5;
            top: calc(100% + 10px);
            right: 0;
            display: grid;
            width: min(340px, calc(100vw - 32px));
            gap: 12px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 14px 40px rgba(24, 39, 67, .16);
        }

        .quick-add-panel h2 {
            margin: 0;
            font-size: 15px;
        }

        .quick-add-panel > input,
        .quick-add-fields input,
        .quick-add-fields select {
            min-width: 0;
            padding: 9px 10px;
            font-size: 12px;
        }

        .quick-add-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px;
        }

        .quick-add-fields label {
            display: grid;
            gap: 5px;
            color: var(--muted);
            font-size: 10px;
        }

        .quick-add-panel .btn {
            width: 100%;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .dashboard-stats {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin: 0 0 16px;
        }

        .dashboard-stats .card {
            padding: 15px 17px 13px;
        }

        .dashboard-stats .stat-value {
            margin-top: 9px;
            font-size: 25px;
        }

        .overall-progress {
            margin-top: 0;
            margin-bottom: 16px;
            padding: 15px 19px;
        }

        .overall-progress .progress {
            margin-top: 11px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.65fr) minmax(285px, .9fr);
            align-items: stretch;
            gap: 16px;
        }

        .dashboard-grid > .panel,
        .dashboard-bottom > .panel {
            min-width: 0;
            margin-top: 0;
        }

        .chart-panel,
        .calendar-panel,
        .recent-panel,
        .activity-panel {
            padding: 18px 19px;
        }

        .chart-panel,
        .calendar-panel {
            min-height: 325px;
        }

        .chart-legend {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #7d8aa0;
            font-size: 10px;
            white-space: nowrap;
        }

        .chart-legend span {
            width: 8px;
            height: 8px;
            border-radius: 3px;
            background: #5b9ae8;
        }

        .chart {
            position: relative;
            height: 235px;
            margin-top: 13px;
            padding: 8px 0 22px;
        }

        .chart-grid {
            position: absolute;
            inset: 9px 0 25px;
            display: grid;
            grid-template-rows: repeat(4, 1fr);
        }

        .chart-grid span {
            border-top: 1px dashed #edf0f5;
        }

        .chart-bars {
            position: relative;
            display: grid;
            height: 100%;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            align-items: end;
            gap: 12px;
        }

        .chart-column {
            display: grid;
            height: 100%;
            grid-template-rows: 18px minmax(0, 1fr) 18px;
            justify-items: center;
            gap: 4px;
        }

        .chart-count,
        .chart-month {
            color: #8a96a9;
            font-size: 10px;
        }

        .chart-bar-track {
            display: flex;
            width: min(34px, 68%);
            height: 100%;
            align-items: end;
            justify-content: center;
        }

        .chart-bar {
            display: block;
            width: 100%;
            min-height: 0;
            border-radius: 6px 6px 2px 2px;
            background: linear-gradient(180deg, #71acef, #4188df);
            transition: height .25s ease, filter .2s ease;
        }

        .chart-bar-track:hover .chart-bar {
            filter: brightness(.92);
        }

        .chart-month {
            font-size: 10px;
        }

        .calendar-panel .panel-head {
            margin-bottom: 10px;
        }

        .calendar-controls {
            display: flex;
            gap: 5px;
        }

        .calendar-controls a {
            display: grid;
            width: 28px;
            height: 28px;
            place-items: center;
            border: 1px solid #e8edf5;
            border-radius: 8px;
            color: #64728a;
            font-size: 20px;
            line-height: 1;
        }

        .calendar-controls a:hover {
            border-color: #c9dcf5;
            background: #f4f8ff;
            color: #2875d2;
        }

        .calendar-month {
            margin: 0 0 12px;
            color: #37445d;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px 2px;
        }

        .calendar-weekday,
        .calendar-day {
            display: grid;
            min-width: 0;
            min-height: 30px;
            place-items: center;
            border-radius: 8px;
            color: #59677e;
            font-size: 10px;
        }

        .calendar-weekday {
            min-height: 22px;
            color: #9aa5b7;
            font-size: 9px;
            font-weight: 700;
        }

        .calendar-day.outside-month {
            color: #c0c8d3;
        }

        .calendar-day.today {
            background: #2875d2;
            color: #fff;
            font-weight: 800;
        }

        .calendar-day.has-tasks:not(.today) {
            position: relative;
            color: #286fc1;
            font-weight: 700;
        }

        .calendar-day.has-tasks:not(.today)::after {
            position: absolute;
            bottom: 2px;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #5897e4;
            content: "";
        }

        .calendar-task-count {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
        }

        .calendar-upcoming {
            display: grid;
            gap: 7px;
            margin-top: 13px;
            padding-top: 11px;
            border-top: 1px solid #eef1f6;
        }

        .calendar-upcoming-title {
            margin: 0;
            color: #8a96a9;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .calendar-upcoming a {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 8px;
            color: #4a5870;
            font-size: 10px;
        }

        .calendar-task-date {
            flex: 0 0 auto;
            color: #3479c9;
            font-weight: 700;
        }

        .calendar-task-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .calendar-empty,
        .activity-empty {
            margin: 15px 0 0;
            color: #8a96a9;
            font-size: 11px;
        }

        .dashboard-bottom {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(275px, .85fr);
            align-items: stretch;
            gap: 16px;
            margin-top: 16px;
        }

        .upcoming-panel {
            margin-top: 16px;
        }

        .upcoming-list {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .upcoming-list li {
            display: grid;
            min-width: 0;
            grid-template-columns: 68px minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 14px;
            padding: 11px 0;
            border-bottom: 1px solid #f0f3f0;
        }

        .upcoming-list li:last-child {
            border-bottom: 0;
        }

        .upcoming-date {
            color: #3479c9;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .upcoming-title {
            overflow: hidden;
            color: #34425b;
            font-size: 12px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upcoming-title:hover {
            color: #2875d2;
        }

        .upcoming-empty {
            margin: 0;
            color: #8a96a9;
            font-size: 12px;
        }

        .dashboard-filters {
            display: grid;
            grid-template-columns: minmax(130px, 1fr) minmax(110px, .7fr) auto;
            align-items: center;
            gap: 8px;
            margin-bottom: 9px;
        }

        .dashboard-filters input,
        .dashboard-filters select {
            min-width: 0;
            height: 35px;
            padding: 0 10px;
            border-color: #e8edf4;
            border-radius: 8px;
            font-size: 11px;
        }

        .dashboard-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .dashboard-search svg {
            position: absolute;
            left: 9px;
            width: 15px;
            height: 15px;
            color: #9aa5b7;
            pointer-events: none;
        }

        .dashboard-search input {
            padding-left: 31px;
        }

        .btn-filter {
            min-height: 35px;
            padding: 0 12px;
            box-shadow: none;
            font-size: 11px;
        }

        .dashboard-bottom table {
            min-width: 440px;
        }

        .dashboard-bottom th {
            padding-top: 9px;
            padding-bottom: 9px;
        }

        .dashboard-bottom td {
            padding-top: 11px;
            padding-bottom: 11px;
        }

        .dashboard-bottom .task-title {
            color: #34425b;
        }

        .dashboard-bottom .task-title:hover {
            color: #2875d2;
        }

        .activity-list {
            display: grid;
            gap: 15px;
            margin: 15px 0 0;
            padding: 0;
            list-style: none;
        }

        .activity-list li {
            display: flex;
            min-width: 0;
            align-items: flex-start;
            gap: 10px;
        }

        .activity-icon {
            display: grid;
            width: 28px;
            height: 28px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 9px;
            background: #edf4ff;
            color: #3479c9;
            font-size: 17px;
            font-weight: 700;
        }

        .activity-icon.activity-completed {
            background: #eaf6ee;
            color: #35835a;
        }

        .activity-icon.activity-deleted {
            background: #fff0ee;
            color: #bd5549;
        }

        .activity-copy {
            display: grid;
            min-width: 0;
            gap: 4px;
            padding-top: 1px;
            color: #68758a;
            font-size: 10px;
            line-height: 1.45;
        }

        .activity-copy strong {
            color: #34425b;
            font-weight: 700;
        }

        .activity-copy time {
            color: #9aa5b7;
            font-size: 9px;
        }

        @media(max-width: 900px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                min-width: 0;
                position: static;
                height: auto;
                min-height: 0;
                padding: 13px 20px 10px;
            }

            .brand {
                padding: 0 0 12px;
            }

            .nav-label, .sidebar-bottom {
                display: none;
            }

            .nav {
                display: flex;
                max-width: 100%;
                min-width: 0;
                gap: 5px;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .nav::-webkit-scrollbar {
                display: none;
            }

            .nav a {
                flex: 0 0 auto;
                min-height: 40px;
                padding: 0 12px;
                white-space: nowrap;
            }

            .main {
                min-width: 0;
                padding-top: 29px;
            }

            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: minmax(0, 1.2fr) minmax(250px, .8fr);
            }

            .dashboard-bottom {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 600px) {
            .sidebar {
                padding-right: 14px;
                padding-left: 14px;
            }

            .main {
                padding: 25px 15px 34px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 16px;
            }

            .topbar .btn {
                width: 100%;
            }

            .dashboard-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .header-actions .quick-add {
                flex: 1;
            }

            .quick-add > summary {
                width: 100%;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .chart-panel,
            .calendar-panel {
                min-height: 0;
            }

            .chart {
                height: 200px;
            }

            .dashboard-filters {
                grid-template-columns: 1fr auto;
            }

            .dashboard-search {
                grid-column: 1 / -1;
            }

            .cards {
                gap: 10px;
            }

            .dashboard-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .upcoming-list li {
                grid-template-columns: 50px minmax(0, 1fr) auto;
                gap: 8px;
            }

            .upcoming-list .priority {
                display: none;
            }

            .card {
                padding: 15px;
            }

            .stat-value {
                font-size: 25px;
            }

            .panel {
                padding: 17px 15px;
            }

            .panel-head {
                align-items: flex-start;
            }

            .text-link {
                margin-top: 2px;
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>
<div class="shell">

    <aside class="sidebar">

        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="m5 12.5 4.2 4L19 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="brand-copy">
                <span>Task<span>Flow</span></span>
                <small>Make work flow</small>
            </span>
        </a>

        <nav class="nav">
            <div class="nav-label">Workspace</div>

            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="4" width="6" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="14" y="4" width="6" height="4" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="14" y="12" width="6" height="8" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="4" y="15" width="6" height="5" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('tasks.index') }}"
               class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M9 6h11M9 12h11M9 18h11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="m4 6 1 1 2-2m-3 7 1 1 2-2m-3 7 1 1 2-2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                My Tasks
            </a>

            <a href="{{ route('dashboard') }}#calendar">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="6" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M8 4v4m8-4v4M4 10h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>

            <a href="{{ route('dashboard') }}#task-report">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M5 19V9m7 10V5m7 14v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Reports
            </a>

            <a href="{{ route('tasks.create') }}" class="{{ request()->routeIs('tasks.create') ? 'active' : '' }}">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                New Task
            </a>

            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                    <path d="m19.4 15 .1.1a1.7 1.7 0 0 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 0 1-3.4 0v-.2a1.7 1.7 0 0 0-2.9-1.2l-.1.1a1.7 1.7 0 0 1-2.4-2.4l.1-.1a1.7 1.7 0 0 0-1.2-2.9H4a1.7 1.7 0 0 1 0-3.4h.2a1.7 1.7 0 0 0 1.2-2.9l-.1-.1a1.7 1.7 0 0 1 2.4-2.4l.1.1a1.7 1.7 0 0 0 2.9-1.2V4a1.7 1.7 0 0 1 3.4 0v.2a1.7 1.7 0 0 0 2.9 1.2l.1-.1a1.7 1.7 0 0 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 0 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2.9Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
                </svg>
                Settings
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="account-summary">
                <span class="account-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="account-details">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->email }}</span>
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-light" style="width:100%">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                        <path d="M8 4H4v12h4m3-3 3-3-3-3m3 3H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>

    </aside>

    <main class="main">
        @yield('content')

    </main>

</div>

@if(session('success'))
    <div class="alert" role="status" aria-live="polite" data-toast>
        <span>{{ session('success') }}</span>
        <button class="toast-close" type="button" aria-label="Dismiss notification" data-toast-close>×</button>
    </div>
@endif

<dialog class="delete-confirmation" id="delete-confirmation" aria-labelledby="delete-confirmation-title">
    <h2 id="delete-confirmation-title">Delete this task?</h2>
    <p>“<span data-delete-task-title></span>” will be permanently deleted. This action cannot be undone.</p>
    <div class="actions">
        <button class="btn btn-light" type="button" data-delete-cancel>Cancel</button>
        <button class="btn btn-danger" type="button" data-delete-confirm>Delete task</button>
    </div>
</dialog>

<script>
    const deleteDialog = document.querySelector('#delete-confirmation');
    const deleteTaskTitle = deleteDialog.querySelector('[data-delete-task-title]');
    let taskFormToDelete = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm-delete')) {
            return;
        }

        if (form.dataset.confirmed === 'true') {
            delete form.dataset.confirmed;
            return;
        }

        event.preventDefault();
        taskFormToDelete = form;
        deleteTaskTitle.textContent = form.dataset.taskTitle ?? 'this task';
        deleteDialog.showModal();
    });

    deleteDialog.querySelector('[data-delete-cancel]').addEventListener('click', () => {
        taskFormToDelete = null;
        deleteDialog.close();
    });

    deleteDialog.querySelector('[data-delete-confirm]').addEventListener('click', () => {
        if (!taskFormToDelete) {
            return;
        }

        const form = taskFormToDelete;
        taskFormToDelete = null;
        form.dataset.confirmed = 'true';
        deleteDialog.close();
        form.requestSubmit();
    });

    const toast = document.querySelector('[data-toast]');

    if (toast) {
        toast.querySelector('[data-toast-close]').addEventListener('click', () => {
            toast.classList.add('toast-dismissed');
        });

        window.setTimeout(() => {
            toast.classList.add('toast-dismissed');
        }, 5000);
    }
</script>
</body>
</html>