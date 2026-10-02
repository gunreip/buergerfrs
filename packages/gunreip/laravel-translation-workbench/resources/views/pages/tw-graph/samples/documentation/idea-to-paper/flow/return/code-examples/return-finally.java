// Inside a class; import java.util.List.
static int doublePositive(int value, List<String> events) {
    try {
        if (value > 0) {
            return value * 2;
        }
        return 0;
    } finally {
        events.add("cleanup");
        value = 0; // Does not change the already evaluated int.
    }
}
