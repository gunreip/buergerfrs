// Illustrates suppression, not a recommended cleanup pattern.
function failAndReturn(events) {
    try {
        events.push('original');
        throw new TypeError('Original failure');
    } finally {
        events.push('cleanup');
        return 7; // Suppresses the pending exception.
    }
}

function demonstrateFinallyReturn(events) {
    let result = null;
    try {
        events.push('outer-try');
        result = failAndReturn(events);
        events.push('received');
    } catch (error) {
        events.push('unreachable-catch');
    }
    events.push('after');
    return result;
}
