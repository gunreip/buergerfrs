import java.util.List;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.TimeUnit;

class AsyncExample {
    static CompletableFuture<Integer> loadAAsync() {
        return CompletableFuture.supplyAsync(
            () -> 20,
            CompletableFuture.delayedExecutor(20, TimeUnit.MILLISECONDS)
        );
    }

    static CompletableFuture<Integer> loadBAsync() {
        return CompletableFuture.supplyAsync(
            () -> 40,
            CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS)
        );
    }

    static CompletionStage<List<Integer>> demonstrateConcurrentAwait() {
        CompletableFuture<Integer> first = loadAAsync();
        CompletableFuture<Integer> second = loadBAsync();
        return CompletableFuture.allOf(first, second).thenApply(ignored -> {
            // Both already completed successfully: these joins do not wait.
            List<Integer> results = List.of(first.join(), second.join());
            System.out.println(results); // [20, 40]
            return results;
        });
    }
}
// Success path only; failure handling is a separate example.
