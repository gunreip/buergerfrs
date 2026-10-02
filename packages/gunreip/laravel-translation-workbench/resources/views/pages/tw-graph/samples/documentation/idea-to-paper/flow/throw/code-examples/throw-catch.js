function demonstrateThrow(events) {
    try {
        events.push('try');
        throw new TypeError('Invalid input');
        events.push('unreachable'); // Skipped after THROW.
    } catch (error) {
        // JavaScript has no typed CATCH clause.
        if (!(error instanceof TypeError)) {
            throw error;
        }
        events.push('caught');
    }
    events.push('after');
}
