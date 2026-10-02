static int BoundedDouble(int value) {
    if (value <= 0) {
        return 0;
    }
    if (value >= 100) {
        return 200;
    }
    int result = value * 2;
    return result;
}
