// loadSettings returns a Map.
const settings = loadSettings();
for (const [key, value] of settings) {
    processSetting(key, value);
}
showSummary();
