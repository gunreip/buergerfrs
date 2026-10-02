function addOne(value) {
    const result = value + 1;
    return result;
}

function demonstrateMultipleCalls() {
    const first = addOne(10);  // Call site 1 resumes here with 11.
    const second = addOne(40); // Call site 2 resumes here with 41.
    console.log({ first, second });
    return [first, second];
}
