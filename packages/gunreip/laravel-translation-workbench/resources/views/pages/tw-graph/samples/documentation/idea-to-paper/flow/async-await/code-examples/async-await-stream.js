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

async function* values(signal, onRead, onCleanup) {
    try {
        for (const value of [10, 20, 30]) {
            signal?.throwIfAborted();
            onRead(value);
            await delay(10, signal);
            yield value;
        }
    } finally {
        await delay(5); // Await resource release even after cancellation.
        onCleanup();
    }
}

async function consumeStream(signal, {
    take = 2, onRead = () => {}, onCleanup = () => {},
} = {}) {
    const result = [];
    for await (const value of values(signal, onRead, onCleanup)) {
        result.push(value);
        if (result.length >= take) break;
    }
    return result;
}
// await consumeStream(new AbortController().signal) returns [10, 20] AFTER cleanup.
// take: Infinity consumes to normal exhaustion; an aborted read still runs FINALLY.
// This producer does not prefetch: the third value is not requested after BREAK.
