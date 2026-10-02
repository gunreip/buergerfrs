static int firstPositive(List<List<Integer>> groups) {
    int groupIndex = 0;
    while (groupIndex < groups.size()) {
        List<Integer> group = groups.get(groupIndex);
        int itemIndex = 0;
        while (itemIndex < group.size()) {
            int item = group.get(itemIndex);
            itemIndex++;
            if (item > 0) {
                return item;
            }
        }
        groupIndex++;
    }
    return 0;
}
