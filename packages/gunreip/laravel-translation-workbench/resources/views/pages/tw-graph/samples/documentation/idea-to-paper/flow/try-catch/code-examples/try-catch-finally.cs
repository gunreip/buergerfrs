try {
    PerformOperation();
    RecordSuccess();
} catch (Exception error) {
    HandleFailure(error);
} finally {
    Cleanup();
}
ContinueProcess();
