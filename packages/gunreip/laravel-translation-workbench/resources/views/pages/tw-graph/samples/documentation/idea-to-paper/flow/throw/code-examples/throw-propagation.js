function validateInput(events) {
    events.push('inner');
    throw new TypeError('Invalid input');
    events.push('unreachable-inner');
}

function demonstratePropagation(events) {
    try {
        events.push('outer-try');
        validateInput(events);
        events.push('unreachable-after-call');
    } catch (error) {
        if (!(error instanceof TypeError)) {
            throw error;
        }
        events.push('outer-caught');
    }
    events.push('after');
}
