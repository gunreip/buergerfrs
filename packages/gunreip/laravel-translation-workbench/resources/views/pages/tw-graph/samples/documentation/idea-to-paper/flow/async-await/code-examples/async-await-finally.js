function loadValueAsync(shouldFail = true) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            if (shouldFail) reject(new Error("Load failed"));
            else resolve(42);
        }, 10);
    });
}

async function demonstrateAwaitWithFinally(shouldFail = true) {
    let result;
    try {
        const pending = loadValueAsync(shouldFail);
        result = await pending; // Rejection throws here; assignment is skipped.
    } catch (error) {
        console.log("CATCH: " + error.message);
        result = 0;
    } finally {
        console.log("FINALLY: cleanup");
    }
    console.log("Continue: " + result);
    return result;
}
// Default: rejection -> CATCH -> FINALLY -> continue with 0.
// Pass false: success -> FINALLY -> continue with 42.
