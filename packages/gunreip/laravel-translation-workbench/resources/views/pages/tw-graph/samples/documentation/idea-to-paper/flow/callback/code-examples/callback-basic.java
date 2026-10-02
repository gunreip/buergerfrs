import java.util.function.IntUnaryOperator;

class CallbackExample {
    static int addOne(int value) {
        return value + 1;
    }

    static int apply(int value, IntUnaryOperator callback) {
        int result = callback.applyAsInt(value);
        return result;
    }

    static void demonstrateCallback() {
        int result = apply(41, CallbackExample::addOne); // Method reference.
        System.out.println(result); // 42
    }
}
