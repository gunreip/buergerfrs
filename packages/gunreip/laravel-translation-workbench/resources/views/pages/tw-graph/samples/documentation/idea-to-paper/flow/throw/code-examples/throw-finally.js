function validateAndCleanup(events) {
    try {
        events.push('inner');
        throw new TypeError('Invalid input');
        events.push('unreachable-inner');
    } catch (error) {
        if (!(error instanceof TypeError)) {
            throw error;
        }
        events.push('inner-log');
        throw error; // Same object, not a new Error.
        events.push('unreachable-after-rethrow');
    } finally {
        events.push('cleanup');
    }
}

function demonstrateFinally(events) {
    try {
        events.push('outer-try');
        validateAndCleanup(events);
        events.push('unreachable-after-call');
    } catch (error) {
        if (!(error instanceof TypeError)) {
            throw error;
        }
        events.push('outer-caught');
    }
    events.push('after');
}
