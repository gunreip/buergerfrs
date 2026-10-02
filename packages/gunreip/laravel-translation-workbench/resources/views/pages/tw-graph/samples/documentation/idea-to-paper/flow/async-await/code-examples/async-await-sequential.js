function loadValueAsync() {
    return new Promise(resolve => {
        setTimeout(() => resolve(20), 10);
    });
}

function doubleValueAsync(value) {
    return new Promise(resolve => {
        setTimeout(() => resolve(value * 2), 10);
    });
}

async function demonstrateSequentialAwaits() {
    const first = loadValueAsync();
    const value = await first;
    const second = doubleValueAsync(value); // Starts only after first completes.
    const result = await second;
    console.log(result); // 40
    return result;
}
