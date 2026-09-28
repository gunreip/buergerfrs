try {
    PerformOperation();
    RecordSuccess();
} catch (Exception error) {
    HandleFailure(error);
}
ContinueProcess();
