// Inside a class; import java.util.ArrayList; import java.util.List.
static List<String> processItems(boolean[] items) {
    List<String> events = new ArrayList<>();
    int index = 0;
    try {
        for (boolean valid : items) {
            if (!valid) throw new IllegalArgumentException("Invalid item");
            events.add("processed:" + index++);
        }
        events.add("completed");
    } catch (IllegalArgumentException error) {
        events.add("caught");
    }
    events.add("after");
    return events;
}
// processItems(new boolean[]{true, false, true}) => [processed:0, caught, after]
