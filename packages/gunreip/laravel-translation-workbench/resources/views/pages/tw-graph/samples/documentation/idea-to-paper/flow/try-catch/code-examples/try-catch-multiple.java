try {
    performOperation();
    recordSuccess();
} catch (ValidationFailure error) {
    handleValidation(error);
} catch (StorageFailure error) {
    handleStorage(error);
} catch (Exception error) {
    handleOther(error);
}
continueProcess();
