import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.TimeUnit;

class AsyncExample {
    static CompletableFuture<Integer> loadValueAsync() {
        return CompletableFuture.supplyAsync(
            () -> 42,
            CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS)
        );
    }

    // Continuation-based equivalent, not Java async/await syntax.
    static CompletionStage<Integer> demonstrateAwait() {
        CompletableFuture<Integer> pending = loadValueAsync();
        return pending.thenApply(result -> {
            System.out.println(result); // 42
            return result;
        });
    }
}
