import java.util.List;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicInteger;

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

    static CompletableFuture<Object> firstCompletion() {
        var a = loadAAsync();
        var b = loadBAsync();
        return CompletableFuture.anyOf(a, b);
    }

    static CompletableFuture<Integer> firstSuccess() {
        var a = loadAAsync();
        var b = loadBAsync();
        var result = new CompletableFuture<Integer>();
        var failures = new AtomicInteger();
        var allFailed = new RuntimeException("All operations failed");
        for (var candidate : List.of(a, b)) {
            candidate.whenComplete((value, error) -> {
                if (error == null) {
                    result.complete(value);
                } else {
                    allFailed.addSuppressed(error);
                    if (failures.incrementAndGet() == 2) {
                        result.completeExceptionally(allFailed);
                    }
                }
            });
        }
        return result;
    }
}
// Invoke separately and observe the returned future's result/error.
// Remaining work is not cancelled. Executor scheduling determines completion order.
