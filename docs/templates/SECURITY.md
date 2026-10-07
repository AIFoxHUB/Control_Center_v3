# 🛡️ Security & Privacy Charta

## 1. Passwort-Sicherheit
- Standard: `PASSWORD_ARGON2ID` mit memory_cost=65536, time_cost=4, threads=1.

## 2. XSS & CSRF Schutz
- `htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8')` bei HTML-Ausgaben.
- SameSite=Lax / Strict für Authentifizierungs-Cookies.

## 3. PII Anonymisierung
- Vor Übergabe an externe LLM-APIs werden Telefonnummern, IBANs und E-Mails maskiert.
