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

async function demonstrateTimeout(timeoutMs = 20) {
    const controller = new AbortController();
    const timeoutReason = new DOMException("Deadline exceeded", "TimeoutError");
    const deadline = setTimeout(() => controller.abort(timeoutReason), timeoutMs);
    let outcome;
    try {
        const pending = loadValueAsync(controller.signal);
        const result = await pending;
        outcome = { status: "completed", value: result };
    } catch (error) {
        if (!controller.signal.aborted || error !== timeoutReason) throw error;
        console.log("CATCH: timeout");
        outcome = { status: "timeout" };
    } finally {
        clearTimeout(deadline);
        console.log("FINALLY: deadline cleared");
    }
    return outcome;
}

// await demonstrateTimeout() -> { status: "timeout" } on the shown path.
// The operation takes 100 ms; the deadline requests cancellation after 20 ms.
// This is cooperative cancellation, not a hard real-time deadline.
