// Inside a class; import java.util.List.
static void demonstrateThrow(List<String> events) {
    try {
        events.add("try");
        throw new IllegalArgumentException("Invalid input");
        // events.add("unreachable"); // Java rejects unreachable statements.
    } catch (IllegalArgumentException error) {
        events.add("caught");
    }
    events.add("after");
}
