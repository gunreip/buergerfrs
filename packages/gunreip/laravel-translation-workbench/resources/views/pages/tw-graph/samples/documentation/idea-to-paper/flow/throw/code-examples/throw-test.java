// Inside a class; import java.util.ArrayList; import java.util.List.
static List<String> processItems(boolean[] items) {
    List<String> events = new ArrayList<>();
    int index = 0;
    for (boolean valid : items) {
        try {
            if (!valid) throw new IllegalArgumentException("Invalid item");
            events.add("processed:" + index);
        } catch (IllegalArgumentException error) {
            events.add("caught:" + index);
        }
        index++;
    }
    events.add("completed");
    events.add("after");
    return events;
}
// [true, false, true] => processed:0, caught:1, processed:2, completed, after
