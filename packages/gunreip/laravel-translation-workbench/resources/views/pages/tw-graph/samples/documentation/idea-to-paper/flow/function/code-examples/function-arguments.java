class FunctionExample {
    static int adjust(int value, int factor) {
        value = value + 1; // Reassigns the local primitive parameter.
        int result = value * factor;
        return result;
    }

    static void demonstrateArguments() {
        int value = 20;
        int factor = 2;
        int result = adjust(value, factor);
        System.out.println("value=" + value + "; factor=" + factor + "; result=" + result);
        // value=20; factor=2; result=42
    }
}
