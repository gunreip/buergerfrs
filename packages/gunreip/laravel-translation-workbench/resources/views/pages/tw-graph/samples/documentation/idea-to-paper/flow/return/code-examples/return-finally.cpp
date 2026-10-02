// C++ has no FINALLY. A scope guard runs before returning to the caller.
int doublePositive(int value, int& cleanupCount)
{
    struct CleanupGuard {
        int& localValue;
        int& count;
        ~CleanupGuard() noexcept {
            ++count;
            localValue = 0;
        }
    } cleanup{value, cleanupCount};

    if (value > 0) {
        return value * 2;
    }
    return 0;
}
