import java.util.concurrent.CompletableFuture;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.function.Function;

record Outcome(Integer value, Throwable error) {}

class AsyncExample {
    static CompletableFuture<Integer> loadAsync(String item) {
        return CompletableFuture.supplyAsync(
            () -> item.equals("A") ? 10 : item.equals("B") ? 20 : 30,
            CompletableFuture.delayedExecutor(item.equals("A") ? 60 : 10,
                TimeUnit.MILLISECONDS));
    }

    static CompletableFuture<Void> worker(String[] items, AtomicInteger next,
            Outcome[] outcomes, Function<String, CompletableFuture<Integer>> operation) {
        int index = next.getAndIncrement();
        if (index >= items.length) return CompletableFuture.completedFuture(null);
        CompletableFuture<Integer> pending;
        try { pending = operation.apply(items[index]); }
        catch (Exception error) { pending = CompletableFuture.failedFuture(error); }
        return pending.handle((value, error) -> {
            outcomes[index] = new Outcome(value, error);
            return null;
        }).thenComposeAsync(ignored -> worker(items, next, outcomes, operation));
    }

    static CompletableFuture<Outcome[]> mapLimited(String[] items, int limit,
            Function<String, CompletableFuture<Integer>> operation) {
        if (limit < 1) throw new IllegalArgumentException("limit must be positive");
        var next = new AtomicInteger();
        var outcomes = new Outcome[items.length];
        var workers = new CompletableFuture<?>[Math.min(limit, items.length)];
        for (int i = 0; i < workers.length; i++) {
            workers[i] = worker(items, next, outcomes, operation);
        }
        return CompletableFuture.allOf(workers).thenApply(ignored -> outcomes);
    }

    static CompletableFuture<Outcome[]> demonstrateLimitedConcurrency() {
        return mapLimited(new String[] {"A", "B", "C"}, 2, AsyncExample::loadAsync);
    }
}
// Executor scheduling affects completion order, but never the active-operation limit.
