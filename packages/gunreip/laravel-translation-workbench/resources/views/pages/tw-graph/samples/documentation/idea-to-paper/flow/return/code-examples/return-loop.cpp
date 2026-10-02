int findFirstPositive(const std::vector<int>& values) {
    int index = 0;
    while (index < static_cast<int>(values.size())) {
        int item = values[index];
        if (item > 0) {
            return index;
        }
        index++;
    }
    return -1;
}
