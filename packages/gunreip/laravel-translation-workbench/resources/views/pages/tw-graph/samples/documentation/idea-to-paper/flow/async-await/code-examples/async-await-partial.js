function loadAAsync() {
    return new Promise(resolve => setTimeout(() => resolve(20), 20));
}

function loadBAsync() {
    return new Promise((resolve, reject) => {
        setTimeout(() => reject(new Error("Load B failed")), 10);
    });
}

async function demonstratePartialSuccess() {
    const first = loadAAsync();
    const second = loadBAsync();
    const outcomes = await Promise.allSettled([first, second]);
    for (const outcome of outcomes) {
        if (outcome.status === "fulfilled") console.log("Value: " + outcome.value);
        else console.log("Error: " + outcome.reason.message);
    }
    return outcomes; // A fulfilled with 20; B rejected with its Error.
}
// Even if B rejects first, allSettled waits for A and retains its result.
