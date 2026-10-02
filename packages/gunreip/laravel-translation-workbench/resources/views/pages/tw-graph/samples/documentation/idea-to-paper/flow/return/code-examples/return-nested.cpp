int firstPositive(const std::vector<std::vector<int>>& groups) {
    int groupIndex = 0;
    while (groupIndex < static_cast<int>(groups.size())) {
        const auto& group = groups[groupIndex];
        int itemIndex = 0;
        while (itemIndex < static_cast<int>(group.size())) {
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
