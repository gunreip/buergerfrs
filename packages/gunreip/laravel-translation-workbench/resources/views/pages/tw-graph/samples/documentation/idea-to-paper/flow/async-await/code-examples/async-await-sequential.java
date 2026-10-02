import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.TimeUnit;

class AsyncExample {
    static CompletableFuture<Integer> loadValueAsync() {
        return CompletableFuture.supplyAsync(
            () -> 20,
            CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS)
        );
    }

    static CompletableFuture<Integer> doubleValueAsync(int value) {
        return CompletableFuture.supplyAsync(
            () -> value * 2,
            CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS)
        );
    }

    // Continuation-based equivalent, not Java async/await syntax.
    static CompletionStage<Integer> demonstrateSequentialAwaits() {
        CompletableFuture<Integer> first = loadValueAsync();
        return first.thenCompose(value -> doubleValueAsync(value))
            .thenApply(result -> {
                System.out.println(result); // 40
                return result;
            });
    }
}
