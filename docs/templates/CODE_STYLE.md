# 💻 Code Style Guidelines (CCv3)

## 1. PHP 8.2+
- `declare(strict_types=1);` wo immer sinnvoll.
- Saubere JSON-Antworten mit `header('Content-Type: application/json; charset=utf-8');`.
- Striktes Parameter-Binding bei SQL (PDO).
- Niemals rohe Fehlermeldungen mit Pfaden an den Client senden (`display_errors = 0`).

## 2. JavaScript / SPA
- Vanilla ES2024 / Modern Async-Await.
- Keine unübersichtlichen Framework-Overheads.
- Saubere Fehlerbehandlung mit `try...catch` und User-Feedback.

## 3. CSS
- CSS Custom Properties aus `00_brand_dna/cyber_design_system.css`.
- Mindestschriftgröße 14px (Badges 11-12px, Fließtext 17px).
- Barrierefreie Kontraste gem. WCAG AAA.
