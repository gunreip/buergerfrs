function processItems(items) {
    const events = [];
    for (const [index, valid] of items.entries()) {
        try {
            if (!valid) throw new TypeError('Invalid item');
            events.push(`processed:${index}`);
        } catch (error) {
            if (!(error instanceof TypeError)) throw error;
            events.push(`caught:${index}`);
        }
    }
    events.push('completed', 'after');
    return events;
}
// [true, false, true] => processed:0, caught:1, processed:2, completed, after
