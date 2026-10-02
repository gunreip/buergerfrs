function computeAndCleanup(events) {
    try {
        events.push('pending-return');
        return 42;
    } finally {
        events.push('cleanup');
        throw new Error('Cleanup failed');
    }
}

function demonstrateFinallyThrow(events) {
    let result = null;
    try {
        events.push('outer-try');
        result = computeAndCleanup(events);
        events.push('unreachable-after-call');
    } catch (error) {
        events.push('outer-caught');
    }
    events.push('after');
    return result; // null, not 42.
}
