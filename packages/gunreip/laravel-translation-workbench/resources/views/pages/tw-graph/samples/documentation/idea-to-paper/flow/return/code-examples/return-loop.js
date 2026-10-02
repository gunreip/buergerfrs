function findFirstPositive(values) {
    let index = 0;
    while (index < values.length) {
        const item = values[index];
        if (item > 0) {
            return index;
        }
        index++;
    }
    return -1;
}
