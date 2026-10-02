int findFirstPositive(const int values[], int count) {
    int index = 0;
    while (index < count) {
        int item = values[index];
        if (item > 0) {
            return index;
        }
        index++;
    }
    return -1;
}
