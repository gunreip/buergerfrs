function boundedDouble(value) {
    if (value <= 0) {
        return 0;
    }
    if (value >= 100) {
        return 200;
    }
    const result = value * 2;
    return result;
}
