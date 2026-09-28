Map<String, String> settings = loadSettings();
for (Map.Entry<String, String> entry : settings.entrySet()) {
    processSetting(entry.getKey(), entry.getValue());
}
showSummary();
