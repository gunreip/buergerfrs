function failAndCleanup(events) {
    let original;
    try {
        events.push('original');
        throw new TypeError('Original failure');
    } catch (error) {
        original = error;
        throw error; // Preserve the pending exception until FINALLY.
    } finally {
        events.push('cleanup');
        throw new Error('Cleanup failed', { cause: original });
    }
}

function demonstrateReplacement(events) {
    let caught = null;
    try {
        events.push('outer-try');
        failAndCleanup(events);
        events.push('unreachable-after-call');
    } catch (error) {
        events.push('outer-caught');
        caught = error; // error.cause is the original TypeError.
    }
    events.push('after');
    return caught;
}
