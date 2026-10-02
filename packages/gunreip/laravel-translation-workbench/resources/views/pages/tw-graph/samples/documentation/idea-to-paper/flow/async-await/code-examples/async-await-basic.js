function loadValueAsync() {
    return new Promise(resolve => {
        setTimeout(() => resolve(42), 10);
    });
}

async function demonstrateAwait() {
    const pending = loadValueAsync(); // Start the operation.
    const result = await pending;    // Suspend this function, not the event loop.
    console.log(result);             // Runs after completion: 42.
    return result;
}

// Call demonstrateAwait() to run the example.
