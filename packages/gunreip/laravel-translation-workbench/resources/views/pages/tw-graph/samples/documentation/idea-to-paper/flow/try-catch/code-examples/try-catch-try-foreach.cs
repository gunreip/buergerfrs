var items = LoadItems();
try {
    foreach (var item in items) {
        ProcessItem(item);
        RecordSuccess(item);
    }
} catch (Exception error) {
    HandleFailure(error);
}
ContinueProcess();
