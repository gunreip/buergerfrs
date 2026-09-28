try {
    performOperation();
    recordSuccess();
} catch (const ValidationFailure& error) {
    handleValidation(error);
} catch (const StorageFailure& error) {
    handleStorage(error);
} catch (...) {
    handleOther();
}
continueProcess();
