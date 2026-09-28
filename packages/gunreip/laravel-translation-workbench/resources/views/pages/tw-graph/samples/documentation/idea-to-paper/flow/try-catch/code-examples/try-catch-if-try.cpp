if (enabled) {
    prepareOperation();
    try {
        performOperation();
        recordSuccess();
    } catch (...) {
        handleFailure();
    }
} else {
    recordSkipped();
}
continueProcess();
