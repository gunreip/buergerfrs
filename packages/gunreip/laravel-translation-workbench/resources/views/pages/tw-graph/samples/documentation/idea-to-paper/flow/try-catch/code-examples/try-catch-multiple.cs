try {
    PerformOperation();
    RecordSuccess();
} catch (ValidationFailure error) {
    HandleValidation(error);
} catch (StorageFailure error) {
    HandleStorage(error);
} catch (Exception error) {
    HandleOther(error);
}
ContinueProcess();
