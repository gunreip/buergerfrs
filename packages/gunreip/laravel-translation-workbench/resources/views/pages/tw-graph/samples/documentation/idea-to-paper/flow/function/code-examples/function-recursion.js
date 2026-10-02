// Small non-negative integers; factorial(0) = 1.
function factorial(n) {
    if (n === 0) {
        return 1;
    }
    const inner = factorial(n - 1);
    return n * inner;
}

function demonstrateRecursion() {
    const result = factorial(2);
    console.log(result); // 2; frames unwind in order: 0, 1, 2.
    return result;
}
