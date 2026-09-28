try {
    if (usePrimary) {
        performPrimary();
    } else {
        performSecondary();
    }
    recordSuccess();
} catch (Exception error) {
    handleFailure(error);
} finally {
    cleanup();
}
continueProcess();
