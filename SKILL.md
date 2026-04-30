# SKILL.md

## Skill: Testdatenbank zurücksetzen

**Beschreibung:**
Setzt die Testdatenbank (SQLite in-memory oder lokale Datei) zurück und führt alle Migrationen aus. Nützlich für automatisierte Tests und lokale Entwicklung.

**Befehle:**
- `php artisan migrate:fresh --seed`

**Wann verwenden:**
- Vor jedem Testlauf, wenn Datenbankzustand garantiert werden muss
- Nach Änderungen an Migrationen oder Seedern

**Hinweis:**
- Funktioniert mit der Standard-Testkonfiguration (`phpunit.xml`)
- Für andere Umgebungen ggf. `.env` anpassen

---

> Diese Skill-Datei kann von AI Agents genutzt werden, um Testdatenbanken zuverlässig zu resetten und ein konsistentes Testumfeld zu gewährleisten.
