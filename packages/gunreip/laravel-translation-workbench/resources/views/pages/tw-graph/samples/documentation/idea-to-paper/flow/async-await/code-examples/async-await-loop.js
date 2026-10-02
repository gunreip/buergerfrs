function transformAsync(item) {
    return new Promise(resolve => {
        setTimeout(() => resolve(item + 1), 10);
    });
}

async function processItems(items) {
    const results = [];
    for (const item of items) {
        const pending = transformAsync(item);
        const result = await pending;
        results.push(result);
    }
    return results;
}

// await processItems([10, 20, 30]) -> [11, 21, 31]
// await processItems([]) -> []; no operation starts.
// A rejection propagates out: subsequent items do not start.
