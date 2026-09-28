try {
    performOperation();
    recordSuccess();
} catch (Exception error) {
    handleFailure(error);
} finally {
    cleanup();
}
continueProcess();
