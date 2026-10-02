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

class TimeoutError extends Error {}

async function retryWithDeadline(operation, {
    totalMs = 50, maxAttempts = 3, delayMs = 20, signal,
} = {}) {
    if (!Number.isFinite(totalMs) || totalMs <= 0) {
        throw new RangeError("totalMs must be positive and finite");
    }
    signal?.throwIfAborted();
    const controller = new AbortController();
    const forwardAbort = () => controller.abort(signal.reason);
    signal?.addEventListener("abort", forwardAbort, {once: true});
    const timer = setTimeout(() => {
        controller.abort(new TimeoutError("Total retry deadline exceeded"));
    }, totalMs); // Created once, not once per attempt.
    try {
        return await retry(operation, {maxAttempts, delayMs, signal: controller.signal});
    } finally {
        clearTimeout(timer);
        signal?.removeEventListener("abort", forwardAbort);
    }
}

async function demonstrateDeadline(signal) {
    return await retryWithDeadline(async (attempt, currentSignal) => {
        await delay(attempt === 1 ? 10 : 100, currentSignal);
        if (attempt === 1) throw new TransientError("Temporarily unavailable");
        return 42;
    }, {totalMs: 50, maxAttempts: 3, delayMs: 20, signal});
}
// One budget: 10ms attempt + 20ms delay + remaining 20ms in attempt 2.
// The caller observes TimeoutError; external cancellation keeps its own reason.
// Timers are subject to scheduler delays; operations must cooperate with cancellation.
