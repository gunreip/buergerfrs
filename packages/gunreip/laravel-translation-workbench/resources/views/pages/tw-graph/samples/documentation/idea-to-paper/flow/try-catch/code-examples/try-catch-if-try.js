if (enabled) {
    prepareOperation();
    try {
        performOperation();
        recordSuccess();
    } catch (error) {
        handleFailure(error);
    }
} else {
    recordSkipped();
}
continueProcess();
