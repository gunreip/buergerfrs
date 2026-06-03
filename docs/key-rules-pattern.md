# Verbindliche Referenz

Diese Datei ist die fachliche Referenz für Translation-Key-Pattern und Translation-Workflow-Regeln.

Bei Änderungen an Translation-List, Target-Language-Logik, Suggested-Key-Regeln oder Translation-Commands gilt:
- zuerst diese Regeln prüfen
- keine stillschweigenden semantischen Umbauten
- bei Abweichungen erst fachlich abstimmen

<style>
    n {color: #dc2626;}
    g {color: #0891b2;}
    se {color: #10b981;}
</style>

# Beispiel-Patterns zur Erstellung von Translation-Keys

|  Translation-Key-Pattern </br> Base (two / three) | real Path / File | resulting Translation-Key | Comment |
| --- | --- | --- | --- |
| <n>namespace</n>.<g>group</g> | `resources/views/components`&hellip; | | two-Part-Prefix |
| <n>namespace</n>.<g>group</g>.<se>section</se> | | | three-Part-Prefix |
| <n>namespace</n>.<g>group</g> | `resources/views/components`/<n>account</n>/⚡<g>preferences</g>.blade.php | <n>account</n>.<g>preferences</g>. &hellip; | |
| <n>namespace</n>.<g>group</g>.<se>section</se> | `resources/views/components`/<n>account</n>/partials/<g>preferences</g>/⚡<se>application-preferences</se>.blade.php | <n>account</n>.<g>preferences</g>.<se>application_preferences</se>. &hellip; |  |
| <n>namespace</n>.<g>group</g>.<se>section</se> | `resources/views/components`/<n>account</n>/partials/<g>preferences</g>/⚡<se>stored-settings</se>.blade.php | <n>account</n>.<g>preferences</g>.<se>stored_settings</se>. &hellip; |  |
| <n>namespace</n>.<g>group</g> | `resources/views/components`/<n>admin</n>/⚡<g>app-settings</g>.blade.php | <n>admin</n>.<g>app_settings</g>. &hellip; |  |
| <n>namespace</n>.<g>group</g>.<se>section</se> | `resources/views/components`/<n>admin</n>/partials/<g>app-settings</g>/⚡<se>table-role-badges</se>.blade.php | <n>admin</n>.<g>app-settings</g>.<se>table-role-badges</se>. &hellip; |  |
| <n>namespace</n>.<g>group</g>.<se>section</se> | `resources/views/components`/<n>admin</n>/partials/<g>app-settings</g>/⚡<se>meta-health</se>.blade.php | <n>admin</n>.<g>app-settings</g>.<se>meta-health</se>. &hellip; |  |

