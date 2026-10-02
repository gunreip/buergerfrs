static int FindFirstPositive(int[] values) {
    int index = 0;
    while (index < values.Length) {
        int item = values[index];
        if (item > 0) {
            return index;
        }
        index++;
    }
    return -1;
}
