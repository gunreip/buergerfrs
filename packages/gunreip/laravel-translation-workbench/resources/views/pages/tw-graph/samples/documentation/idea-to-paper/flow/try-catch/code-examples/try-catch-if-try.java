if (enabled) {
    prepareOperation();
    try {
        performOperation();
        recordSuccess();
    } catch (Exception error) {
        handleFailure(error);
    }
} else {
    recordSkipped();
}
continueProcess();
