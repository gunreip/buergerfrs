function addOne(value) {
    const result = value + 1;
    return result; // 21: resumes doubleAdjusted().
}

function doubleAdjusted(value) {
    const adjusted = addOne(value);
    const result = adjusted * 2;
    return result; // 42: resumes the original caller.
}

function demonstrateNested() {
    const value = 20;
    const result = doubleAdjusted(value);
    console.log(result);
    return result;
}
