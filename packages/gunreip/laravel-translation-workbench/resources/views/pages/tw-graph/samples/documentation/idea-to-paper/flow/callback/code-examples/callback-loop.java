import java.util.ArrayList;
import java.util.List;
import java.util.function.IntUnaryOperator;

class CallbackExample {
    static int addOne(int value) {
        return value + 1;
    }

    static List<Integer> mapEach(List<Integer> items, IntUnaryOperator callback) {
        List<Integer> results = new ArrayList<>();
        for (int item : items) {
            int result = callback.applyAsInt(item);
            results.add(result);
        }
        return results;
    }

    static List<Integer> demonstrateCallbackLoop() {
        return mapEach(List.of(10, 20, 30), CallbackExample::addOne); // [11, 21, 31]
    }
}
