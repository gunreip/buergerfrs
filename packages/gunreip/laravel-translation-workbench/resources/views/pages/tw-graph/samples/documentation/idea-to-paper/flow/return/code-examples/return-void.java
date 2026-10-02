// Inside a class; import java.util.List.
static void notifyIfEnabled(boolean enabled, List<String> events) {
    if (!enabled) {
        return;
    }
    events.add("notified");
    return; // Optional at the end of this void method.
}
