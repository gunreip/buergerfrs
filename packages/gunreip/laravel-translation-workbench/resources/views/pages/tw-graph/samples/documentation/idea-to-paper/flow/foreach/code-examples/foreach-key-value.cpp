// C++17: structured bindings over a map.
auto settings = loadSettings();
for (const auto& [key, value] : settings) {
    processSetting(key, value);
}
showSummary();
