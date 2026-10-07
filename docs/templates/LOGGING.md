# 📜 Logging & Audit Guidelines

## 1. Log-Formate
- Alle Logs werden als strukturierte JSON Lines (`logs/app.log`) gespeichert.
- Felder: `timestamp`, `level` (INFO, WARN, ERROR, CRITICAL), `channel`, `message`, `context`.

## 2. Retention
- Tägliche Rotation, max. 30 Tage Aufbewahrung.
- Keine Ablage von Klartext-Passwörtern oder Roh-Tokens.
