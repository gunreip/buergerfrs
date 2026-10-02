import java.util.function.IntUnaryOperator;

class CallbackExample {
    static int addOne(int value) {
        return value + 1;
    }

    static int doubleValue(int value) {
        return value * 2;
    }

    static int apply(int value, IntUnaryOperator callback) {
        int result = callback.applyAsInt(value);
        return result;
    }

    static void demonstrateCallbacks() {
        int first = apply(20, CallbackExample::addOne);
        int second = apply(20, CallbackExample::doubleValue);
        System.out.println("first=" + first + "; second=" + second); // 21 and 40
    }
}
