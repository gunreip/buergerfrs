var queue = LoadQueue();
PrepareQueue(queue);
while (HasNext(queue)) {
    var item = NextItem(queue);
    try {
        ProcessItem(item);
        RecordSuccess(item);
    } catch (Exception error) {
        HandleItemFailure(item, error);
    } finally {
        CleanupItem(item);
    }
}
ShowSummary();
