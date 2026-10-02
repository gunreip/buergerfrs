/* load_items returns an array and writes its element count. */
size_t count = 0;
Item *items = load_items(&count);
size_t index = 0;
while (index < count) {
    Item item = items[index];
    if (may_process(item)) {
        process_item(item);
        index++;
    } else {
        break;
    }
}
continue_process();
