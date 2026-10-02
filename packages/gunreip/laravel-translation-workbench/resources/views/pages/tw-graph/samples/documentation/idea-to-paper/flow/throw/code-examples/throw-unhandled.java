// Inside a class.
static void failWithCleanup() {
    try {
        System.out.println("original");
        throw new IllegalArgumentException("Original failure");
    } finally {
        System.out.println("cleanup");
    }
}

static void demonstrateUnhandled() {
    System.out.println("call");
    failWithCleanup(); // No local CATCH: propagates to the caller.
    System.out.println("unreachable-after-call");
}
