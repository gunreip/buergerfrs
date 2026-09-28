try {
    performOperation();
    recordSuccess();
} catch (error) {
    handleFailure(error);
} finally {
    cleanup();
}
continueProcess();
