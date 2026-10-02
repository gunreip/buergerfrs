function addOne(value) {
    return value + 1;
}

function apply(value, callback) {
    const result = callback(value);
    return result;
}

function demonstrateCallback() {
    const result = apply(41, addOne); // Pass the function, not addOne(41).
    console.log(result); // 42
    return result;
}
