// Standard C++ scope guard: cleanup runs when this scope ends.
{
    struct CleanupGuard {
        ~CleanupGuard() noexcept { cleanup(); }
    } cleanupGuard;
    try {
        performOperation();
        recordSuccess();
    } catch (...) {
        handleFailure();
    }
}
continueProcess();
