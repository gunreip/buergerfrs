class FunctionExample {
    static void logMessage(String message) {
        System.out.println(message);
    }

    static void demonstrateVoid() {
        System.out.println("before");
        String message = "Processed item";
        logMessage(message); // No result assignment.
        System.out.println("after");
    }
}
