if (enabled) {
    PrepareOperation();
    try {
        PerformOperation();
        RecordSuccess();
    } catch (Exception error) {
        HandleFailure(error);
    }
} else {
    RecordSkipped();
}
ContinueProcess();
