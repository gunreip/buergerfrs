function addOne(value) {
    return value + 1;
}

function doubleValue(value) {
    return value * 2;
}

function apply(value, callback) {
    const result = callback(value);
    return result;
}

function demonstrateCallbacks() {
    const first = apply(20, addOne);
    const second = apply(20, doubleValue);
    console.log({ first, second }); // 21 and 40
    return [first, second];
}
