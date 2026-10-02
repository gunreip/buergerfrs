/* C has no FINALLY: perform cleanup before applying pending BREAK. */
size_t count = 0;
Item *items = load_items(&count);
size_t index = 0;
while (index < count) {
    Item item = items[index];
    bool pending_break = false;
    if (may_process(item)) {
        process_item(item);
        index++;
    } else {
        pending_break = true;
    }
    cleanup_item(item);
    if (pending_break) {
        break;
    }
}
show_summary();
