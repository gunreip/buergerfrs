class TransientError extends Error {}

function delay(ms, signal) {
    return new Promise((resolve, reject) => {
        signal.throwIfAborted();
        const onAbort = () => {
            clearTimeout(timer);
            signal.removeEventListener("abort", onAbort);
            reject(signal.reason);
        };
        const timer = setTimeout(() => {
            signal.removeEventListener("abort", onAbort);
            resolve();
        }, ms);
        signal.addEventListener("abort", onAbort, {once: true});
    });
}

async function retry(operation, {maxAttempts = 3, delayMs = 20, signal}) {
    if (!Number.isInteger(maxAttempts) || maxAttempts < 1 ||
        !Number.isFinite(delayMs) || delayMs < 0) {
        throw new RangeError("Invalid retry configuration");
    }
    for (let attempt = 1; attempt <= maxAttempts; attempt++) {
        signal.throwIfAborted();
        try {
            const value = await operation(attempt, signal);
            signal.throwIfAborted();
            return value;
        } catch (error) {
            signal.throwIfAborted();
            if (!(error instanceof TransientError) || attempt === maxAttempts) {
                throw error;
            }
            await delay(delayMs, signal);
        }
    }
}

async function demonstrateRetry(signal) {
    return await retry(async (attempt, currentSignal) => {
        await delay(10, currentSignal); // Simulated cancellable operation.
        if (attempt === 1) throw new TransientError("Temporarily unavailable");
        return 42;
    }, {maxAttempts: 3, delayMs: 20, signal});
}
// const controller = new AbortController();
// const pending = demonstrateRetry(controller.signal);
// controller.abort(); // Optional: interrupts operation or retry delay.
// await pending;       // 42 on success; otherwise the final error/abort reason.
// The operation must cooperate with cancellation and be safe to repeat.
