function doublePositive(value, events) {
    try {
        if (value > 0) {
            return value * 2;
        }
        return 0;
    } finally {
        events.push('cleanup');
        value = 0; // Does not change the already evaluated number.
    }
}
