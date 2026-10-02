function loadValueAsync(signal) {
    return new Promise((resolve, reject) => {
        if (signal.aborted) {
            reject(signal.reason);
            return;
        }
        const onAbort = () => {
            clearTimeout(timer);
            signal.removeEventListener("abort", onAbort);
            reject(signal.reason);
        };
        const timer = setTimeout(() => {
            signal.removeEventListener("abort", onAbort);
            resolve(42);
        }, 100);
        signal.addEventListener("abort", onAbort, { once: true });
    });
}

async function demonstrateCancellation(signal) {
    let outcome;
    try {
        const pending = loadValueAsync(signal);
        const result = await pending;
        outcome = { status: "completed", value: result };
    } catch (error) {
        if (!signal.aborted || error !== signal.reason) throw error;
        console.log("CATCH: cancelled");
        outcome = { status: "cancelled" };
    } finally {
        console.log("FINALLY: cleanup");
    }
    return outcome;
}

// The caller requests cancellation while the function is suspended:
// const controller = new AbortController();
// const pending = demonstrateCancellation(controller.signal);
// controller.abort();
// const outcome = await pending; // { status: "cancelled" }
