try {
    if (usePrimary) {
        PerformPrimary();
    } else {
        PerformSecondary();
    }
    RecordSuccess();
} catch (Exception error) {
    HandleFailure(error);
} finally {
    Cleanup();
}
ContinueProcess();
