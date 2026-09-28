try {
    performOperation();
    recordSuccess();
} catch (...) {
    handleFailure();
}
continueProcess();
