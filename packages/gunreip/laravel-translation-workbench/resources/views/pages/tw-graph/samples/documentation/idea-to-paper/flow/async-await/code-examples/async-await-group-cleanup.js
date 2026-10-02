function delay(ms, signal) {
    return new Promise((resolve, reject) => {
        signal?.throwIfAborted();
        const onAbort = () => {
            clearTimeout(timer);
            signal.removeEventListener("abort", onAbort);
            reject(signal.reason);
        };
        const timer = setTimeout(() => {
            signal?.removeEventListener("abort", onAbort);
            resolve();
        }, ms);
        signal?.addEventListener("abort", onAbort, {once: true});
    });
}

async function loadA(signal) {
    try {
        await delay(10, signal);
        throw new Error("A failed");
    } finally {
        await delay(5); // Cleanup deliberately uses no cancelled signal.
    }
}

async function loadB(signal) {
    try {
        await delay(100, signal);
        return 20;
    } finally {
        await delay(5); // Group completion must wait for this cleanup too.
    }
}

async function runGroup(first = loadA, second = loadB) {
    const controller = new AbortController();
    let failed = false;
    let originalError;
    async function observe(operation) {
        try {
            return await operation(controller.signal);
        } catch (error) {
            if (!failed) {
                failed = true;
                originalError = error;
                controller.abort(error);
            }
            throw error;
        }
    }
    const a = observe(first);
    const b = observe(second);
    const outcomes = await Promise.allSettled([a, b]);
    if (failed) throw originalError;
    return outcomes.map(outcome => outcome.value);
}
// Caller: try { await runGroup(); } catch (error) { /* A failed; cleanup finished. */ }
// Aborting alone is insufficient: every operation must observe the signal.
