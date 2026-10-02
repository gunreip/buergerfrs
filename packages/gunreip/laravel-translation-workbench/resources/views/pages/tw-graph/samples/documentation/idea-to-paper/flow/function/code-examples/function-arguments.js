function adjust(value, factor) {
    value = value + 1; // Reassigns the local numeric parameter.
    const result = value * factor;
    return result;
}

function demonstrateArguments() {
    const value = 20;
    const factor = 2;
    const result = adjust(value, factor);
    console.log({ value, factor, result }); // { value: 20, factor: 2, result: 42 }
    return [value, factor, result];
}
