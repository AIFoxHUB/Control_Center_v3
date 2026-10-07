# 🎯 Control Center v3 • Master Engineering Standards

## 1. 🌟 Architektur-Philosophie
- **Moderne SPA-Architektur:** Schnelle Ladezeiten, dynamischer View-Router (`views/*.html`), entkoppeltes REST-Backend (`api/*.php`).
- **High-Visibility UI-DNA:** `#0A0A0F` Hintergrund, `#C8FF00` Primärakzente, `#00F0FF` Sekundärfarben, 17px Basis-Schriftgröße und Touch-Targets >= 44px.
- **0,00 € Autarkie & HP-Worker Delegation:** Nutzung des lokalen HP-Servers (`192.168.2.210` Ollama LLaMA & Qwen Coder) zur Token-Schonung.

## 2. 🛡️ Daten- & Sicherheits-Guardrails
- **Passwörter & Hashes:** Ausschließlich Argon2id / Bcrypt (`PASSWORD_ARGON2ID`).
- **Token Authentication:** Cryptographic Secure JWT / Bearer Tokens (`X-Auth-Token`) und isolierte Sitzungen.
- **PII-Schutz:** Sensible Daten vor Inferenz strikt anonymisieren.
- **EU AI Act Konformität:** Automatisierte Kennzeichnung aller synthetischen Medien.

## 3. ⚙️ Monotonie & Team Memory
- **Fortlaufende Handover-Zählung:** Keine Zähler-Kollisionen, atomare Aktualisierung von `system-status.json` und `handovers.json`.
- **Pre-Flight Integrität:** 7-Stufen QA-Check vor jedem Release.
