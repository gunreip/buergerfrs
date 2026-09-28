try {
    performOperation();
    recordSuccess();
} catch (Exception error) {
    handleFailure(error);
}
continueProcess();
