class FunctionExample {
    // Small non-negative integers; factorial(0) = 1.
    static int factorial(int n) {
        if (n == 0) {
            return 1;
        }
        int inner = factorial(n - 1);
        return n * inner;
    }

    static void demonstrateRecursion() {
        int result = factorial(2);
        System.out.println(result); // 2; frames unwind: 0, 1, 2.
    }
}
