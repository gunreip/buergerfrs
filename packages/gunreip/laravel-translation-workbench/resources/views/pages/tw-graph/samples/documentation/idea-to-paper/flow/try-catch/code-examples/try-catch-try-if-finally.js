try {
    if (usePrimary) {
        performPrimary();
    } else {
        performSecondary();
    }
    recordSuccess();
} catch (error) {
    handleFailure(error);
} finally {
    cleanup();
}
continueProcess();
