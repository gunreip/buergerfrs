static int FirstPositive(List<List<int>> groups) {
    int groupIndex = 0;
    while (groupIndex < groups.Count) {
        List<int> group = groups[groupIndex];
        int itemIndex = 0;
        while (itemIndex < group.Count) {
            int item = group[itemIndex];
            itemIndex++;
            if (item > 0) {
                return item;
            }
        }
        groupIndex++;
    }
    return 0;
}
