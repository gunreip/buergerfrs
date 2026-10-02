class FunctionExample {
    static int addOne(int value) {
        int result = value + 1;
        return result;
    }

    static void demonstrateMultipleCalls() {
        int first = addOne(10);  // Call site 1 resumes here with 11.
        int second = addOne(40); // Call site 2 resumes here with 41.
        System.out.println("first=" + first + "; second=" + second);
    }
}
