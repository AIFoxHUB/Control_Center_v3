# 🗄️ Database Standards (SQLite & Postgres)

## 1. Multi-Engine Unterstützung
- **Entwicklung & Lokaler Stand:** SQLite (`data/ccv3_master.sqlite`).
- **Produktion / Cloud:** PostgreSQL / Supabase REST.

## 2. Tabellen-Konventionen
- Tabellennamen: snake_case im Plural (`users`, `projects`, `assets`).
- Primärschlüssel: `id` (INTEGER AUTOINCREMENT oder UUID).
- Pflicht-Spalten: `created_at DATETIME DEFAULT CURRENT_TIMESTAMP`, `updated_at DATETIME`.
