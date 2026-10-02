void notifyIfEnabled(bool enabled, int& notificationCount)
{
    if (!enabled) {
        return;
    }
    ++notificationCount;
    return; // Optional at the end of this void function.
}
