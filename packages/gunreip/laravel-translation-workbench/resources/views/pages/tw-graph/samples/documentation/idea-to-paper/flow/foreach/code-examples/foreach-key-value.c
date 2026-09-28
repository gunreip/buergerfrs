// C has no native foreach or map. Entry has key and value fields.
size_t count = 0;
Entry *settings = load_settings(&count);
for (size_t i = 0; i < count; ++i) {
    process_setting(settings[i].key, settings[i].value);
}
show_summary();
