# 🌐 REST API Spezifikation (CCv3)

## 1. Basis-Endpunkt & Format
- Alle Endpunkte antworten mit standardisiertem JSON:
```json
{
  "success": true,
  "data": {},
  "timestamp": "2026-10-07T21:00:00Z"
}
```
- Im Fehlerfall:
```json
{
  "success": false,
  "error": "Fehlerbeschreibung",
  "code": 400
}
```

## 2. Authentifizierung
- Header: `X-Auth-Token: <token>` oder Session-Cookie.
