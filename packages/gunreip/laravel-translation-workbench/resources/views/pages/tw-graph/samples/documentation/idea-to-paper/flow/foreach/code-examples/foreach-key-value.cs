// LoadSettings returns a dictionary.
var settings = LoadSettings();
foreach (var entry in settings)
{
    ProcessSetting(entry.Key, entry.Value);
}
ShowSummary();
