function validateAndLog(events) {
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
    }
}

function demonstrateRethrow(events) {
    try {
        events.push('outer-try');
        validateAndLog(events);
        events.push('unreachable-after-call');
    } catch (error) {
        if (!(error instanceof TypeError)) {
            throw error;
        }
        events.push('outer-caught');
    }
    events.push('after');
}
