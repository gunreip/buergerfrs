function loadAsync(item) {
    const delays = {A: 60, B: 10, C: 10};
    const values = {A: 10, B: 20, C: 30};
    return new Promise(resolve =>
        setTimeout(() => resolve(values[item]), delays[item]));
}

async function mapLimited(items, limit, operation) {
    if (!Number.isInteger(limit) || limit < 1) {
        throw new RangeError("limit must be a positive integer");
    }
    const outcomes = new Array(items.length);
    let next = 0;
    async function worker() {
        while (next < items.length) {
            // Claim the next item before yielding at await.
            const index = next++;
            try {
                outcomes[index] = {
                    status: "fulfilled",
                    value: await operation(items[index]),
                };
            } catch (error) {
                outcomes[index] = {status: "rejected", error};
            }
        }
    }
    const workers = Array.from(
        {length: Math.min(limit, items.length)}, () => worker());
    await Promise.all(workers);
    return outcomes;
}

async function demonstrateLimitedConcurrency() {
    return await mapLimited(["A", "B", "C"], 2, loadAsync);
}
// Queue items, not already-started promises. Failures free a slot too.
// Results stay in input order. The limit bounds in-flight operations, not threads.
