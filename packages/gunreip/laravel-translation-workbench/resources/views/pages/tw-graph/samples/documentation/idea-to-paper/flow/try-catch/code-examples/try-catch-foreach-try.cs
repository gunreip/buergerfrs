var items = LoadItems();
foreach (var item in items) {
    try {
        ProcessItem(item);
        RecordSuccess(item);
    } catch (Exception error) {
        HandleItemFailure(item, error);
    }
}
ShowSummary();
