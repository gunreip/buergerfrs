import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.TimeUnit;

class AsyncExample {
    static CompletableFuture<Integer> transformAsync(int item) {
        return CompletableFuture.supplyAsync(
            () -> item + 1,
            CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS)
        );
    }

    static CompletionStage<List<Integer>> processItems(List<Integer> items) {
        CompletableFuture<List<Integer>> chain =
            CompletableFuture.completedFuture(new ArrayList<>());
        for (int item : items) {
            // Building this chain does not start all operations at once.
            chain = chain.thenCompose(results -> transformAsync(item).thenApply(result -> {
                results.add(result);
                return results;
            }));
        }
        return chain;
    }
}
// processItems(List.of(10, 20, 30)) completes with [11, 21, 31].
// Empty input returns an empty list; failure skips later transformations.
