try {
    performOperation();
    recordSuccess();
} catch (error) {
    if (error instanceof ValidationFailure) {
        handleValidation(error);
    } else if (error instanceof StorageFailure) {
        handleStorage(error);
    } else {
        handleOther(error);
    }
}
continueProcess();
