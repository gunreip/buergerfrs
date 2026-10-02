static int findFirstPositive(int[] values) {
    int index = 0;
    while (index < values.length) {
        int item = values[index];
        if (item > 0) {
            return index;
        }
        index++;
    }
    return -1;
}
