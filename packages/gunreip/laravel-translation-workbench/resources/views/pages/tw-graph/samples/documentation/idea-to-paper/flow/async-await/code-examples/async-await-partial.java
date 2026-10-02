import java.util.List;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.TimeUnit;

record Outcome(String status, Integer value, Throwable error) {}

class AsyncExample {
    static CompletableFuture<Integer> loadAAsync() {
        return CompletableFuture.supplyAsync(() -> 20,
            CompletableFuture.delayedExecutor(20, TimeUnit.MILLISECONDS));
    }

    static CompletableFuture<Integer> loadBAsync() {
        return CompletableFuture.supplyAsync(() -> {
            throw new IllegalStateException("Load B failed");
        }, CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS));
    }

    static CompletableFuture<Outcome> observe(CompletableFuture<Integer> operation) {
        return operation.handle((value, error) -> error == null
            ? new Outcome("fulfilled", value, null)
            : new Outcome("rejected", null, error));
    }

    static CompletionStage<List<Outcome>> demonstratePartialSuccess() {
        CompletableFuture<Integer> first = loadAAsync();
        CompletableFuture<Integer> second = loadBAsync();
        CompletableFuture<Outcome> a = observe(first);
        CompletableFuture<Outcome> b = observe(second);
        return CompletableFuture.allOf(a, b).thenApply(ignored -> {
            List<Outcome> outcomes = List.of(a.join(), b.join());
            outcomes.forEach(System.out::println);
            return outcomes;
        });
    }
}
// Success/failure example; cancellation is not modelled here.
