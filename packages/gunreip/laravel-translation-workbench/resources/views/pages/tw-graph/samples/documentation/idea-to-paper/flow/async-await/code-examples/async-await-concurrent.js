function loadAAsync() {
    return new Promise(resolve => setTimeout(() => resolve(20), 20));
}

function loadBAsync() {
    return new Promise(resolve => setTimeout(() => resolve(40), 10));
}

async function demonstrateConcurrentAwait() {
    const first = loadAAsync();
    const second = loadBAsync(); // Starts before awaiting first.
    const results = await Promise.all([first, second]);
    console.log(results); // [20, 40], regardless of completion order.
    return results;
}

// Success path: both fulfill. Rejection handling is a separate example.
