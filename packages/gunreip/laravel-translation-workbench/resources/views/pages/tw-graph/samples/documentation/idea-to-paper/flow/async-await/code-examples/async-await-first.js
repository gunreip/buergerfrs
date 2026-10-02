function loadAAsync() {
    return new Promise(resolve => setTimeout(() => resolve(20), 20));
}

function loadBAsync() {
    return new Promise((resolve, reject) =>
        setTimeout(() => reject(new Error("Load B failed")), 10));
}

async function demonstrateFirstCompletion() {
    const a = loadAAsync();
    const b = loadBAsync();
    try {
        return {status: "fulfilled", value: await Promise.race([a, b])};
    } catch (error) {
        return {status: "rejected", error};
    }
}

async function demonstrateFirstSuccess() {
    const a = loadAAsync();
    const b = loadBAsync();
    try {
        return {status: "fulfilled", value: await Promise.any([a, b])};
    } catch (error) {
        // AggregateError contains every rejection if no operation succeeds.
        return {status: "rejected", error};
    }
}
// Invoke these demonstrations separately. Remaining work is not cancelled.
// Both combinators observe rejections from every supplied promise.
