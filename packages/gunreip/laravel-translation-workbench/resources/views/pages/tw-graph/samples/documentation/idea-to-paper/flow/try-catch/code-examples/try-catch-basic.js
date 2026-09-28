try {
    performOperation();
    recordSuccess();
} catch (error) {
    handleFailure(error);
}
continueProcess();
