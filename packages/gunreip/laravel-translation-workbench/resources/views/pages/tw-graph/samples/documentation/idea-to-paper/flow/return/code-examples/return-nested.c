typedef struct { const int *items; int count; } Group;

int firstPositive(const Group groups[], int groupCount) {
    int groupIndex = 0;
    while (groupIndex < groupCount) {
        Group group = groups[groupIndex];
        int itemIndex = 0;
        while (itemIndex < group.count) {
            int item = group.items[itemIndex];
            itemIndex++;
            if (item > 0) {
                return item;
            }
        }
        groupIndex++;
    }
    return 0;
}
