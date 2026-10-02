function notifyIfEnabled(enabled, events) {
    if (!enabled) {
        return; // The call evaluates to undefined.
    }
    events.push('notified');
    return; // Optional; falling through also yields undefined.
}
