function addOne(value) {
    return value + 1;
}

function mapEach(items, callback) {
    const results = [];
    for (const item of items) {
        const result = callback(item);
        results.push(result);
    }
    return results;
}

function demonstrateCallbackLoop() {
    return mapEach([10, 20, 30], addOne); // [11, 21, 31]
}
