/* load_items returns an array and writes its element count. */
size_t count = 0;
Item *items = load_items(&count);
for (size_t index = 0; index < count; index++) {
    Item item = items[index];
    if (should_skip(item)) {
        continue;
    }
    process_item(item);
}
show_summary();
