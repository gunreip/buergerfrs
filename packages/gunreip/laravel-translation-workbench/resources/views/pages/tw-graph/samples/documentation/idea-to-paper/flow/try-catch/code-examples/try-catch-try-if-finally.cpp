{
    struct CleanupGuard {
        ~CleanupGuard() noexcept { cleanup(); }
    } cleanupGuard;
    try {
        if (usePrimary) {
            performPrimary();
        } else {
            performSecondary();
        }
        recordSuccess();
    } catch (...) {
        handleFailure();
    }
}
continueProcess();
