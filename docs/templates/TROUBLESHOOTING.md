# 🔧 Troubleshooting & Notfall-Protokoll

## 1. Pre-Flight Check schlägt fehl
1. `php -l <datei>` auf fehlerhafte PHP-Syntax prüfen.
2. Zähler-Kette in `data/handovers.json` und `docs/` verifizieren.
3. Fehlende Registrierung in `deploy_critical.py` ergänzen.

## 2. HP-Worker Offline (192.168.2.210)
1. Ping auf `192.168.2.210` ausführen.
2. Ollama Status prüfen: `curl http://192.168.2.210:11434/api/version`.
3. Automatischer Fallback auf Universal AI Gateway greift transparent.
