function processItems(items) {
    const events = [];
    try {
        for (const [index, valid] of items.entries()) {
            if (!valid) throw new TypeError('Invalid item');
            events.push(`processed:${index}`);
        }
        events.push('completed');
    } catch (error) {
        if (!(error instanceof TypeError)) throw error;
        events.push('caught');
    }
    events.push('after');
    return events;
}
// processItems([true, false, true]) => ['processed:0', 'caught', 'after']
