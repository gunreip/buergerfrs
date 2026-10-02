function firstPositive(groups) {
    let groupIndex = 0;
    while (groupIndex < groups.length) {
        const group = groups[groupIndex];
        let itemIndex = 0;
        while (itemIndex < group.length) {
            const item = group[itemIndex];
            itemIndex++;
            if (item > 0) {
                return item;
            }
        }
        groupIndex++;
    }
    return 0;
}
