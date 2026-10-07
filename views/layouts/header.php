<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDMS — Пользователи и документы</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f4f6f9;
            color: #2c3e50;
            padding: 24px;
        }

        .container { max-width: 1100px; margin: 0 auto; }

        .page-title {
            font-size: 22px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e1e5eb;
        }

        .section { margin-bottom: 48px; }

        /* --- поиск --- */
        .search-form {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .search-form input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #cfd6e0;
            border-radius: 6px;
            width: 320px;
            font-size: 14px;
        }

        /* --- кнопки --- */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            background: #3498db;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background: #2c81b7; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }

        /* --- таблицы --- */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }

        th, td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid #eef1f5;
            font-size: 14px;
        }

        th {
            background: #f8f9fb;
            font-weight: 600;
            color: #55606e;
        }

        tr:hover td { background: #fafbfd; }

        td.actions { white-space: nowrap; }
        td.actions a { margin-right: 10px; font-size: 13px; }

        a { color: #3498db; }
        a.danger { color: #e74c3c; }

        .add-link { display: inline-block; margin-top: 12px; font-size: 14px; }

        /* --- плашка фильтра по пользователю --- */
        .filter-banner {
            background: #eaf4fd;
            border: 1px solid #bcdcf5;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 14px;
        }

        /* --- ошибки формы --- */
        .errors {
            background: #fdecea;
            border: 1px solid #f5c6c2;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 16px;
            list-style-position: inside;
            color: #b03a2e;
            font-size: 14px;
        }

        /* --- формы --- */
        .form-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            padding: 24px;
            max-width: 560px;
        }

        .form-group { margin-bottom: 14px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #55606e;
        }

        .form-group label .req { color: #e74c3c; }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #cfd6e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea { min-height: 90px; resize: vertical; }

        /* --- пагинация --- */
        .pagination { margin-top: 14px; font-size: 14px; }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 5px 11px;
            margin-right: 4px;
            border: 1px solid #cfd6e0;
            border-radius: 5px;
            text-decoration: none;
            background: #fff;
        }
        .pagination span.current {
            background: #3498db;
            border-color: #3498db;
            color: #fff;
        }
        .pagination .disabled {
            color: #aab4c0;
            pointer-events: none;
        }

        /* --- логи --- */
        .muted { color: #8a94a6; font-size: 13px; }

        .log-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .log {
            background: #1e1e2e;
            color: #cdd6f4;
            border-radius: 8px;
            padding: 14px 16px;
            font-family: "SF Mono", Menlo, Consolas, monospace;
            font-size: 12.5px;
            line-height: 1.5;
            max-height: 70vh;
            overflow: auto;
        }

        .log-line {
            white-space: pre-wrap;
            word-break: break-word;
            padding: 2px 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .log-line:last-child { border-bottom: none; }
        .log-error { color: #f38ba8; }
        .log-warning { color: #f9e2af; }
        .log-fatal { color: #eba0ac; font-weight: 700; }
    </style>
</head>
<body>
<div class="container">
    <?php if (!empty($_SESSION['user_id'])): ?>
    <div style="display:flex; justify-content:flex-end; align-items:center; gap:10px; font-size:14px; margin-bottom:8px;">
        <span><?= e($_SESSION['user_fio'] ?? '') ?></span>
        <a href="/logs">Логи</a>
        <a href="/logout" style="color:#e74c3c;">Выйти</a>
    </div>
<?php endif; ?>
