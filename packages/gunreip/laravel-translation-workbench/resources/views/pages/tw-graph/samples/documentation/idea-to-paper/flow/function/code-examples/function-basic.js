function addOne(value) {
    const result = value + 1;
    return result;
}

function demonstrateCall() {
    const value = 41;
    const result = addOne(value);
    console.log(result); // 42, after addOne has returned.
    return result;
}
