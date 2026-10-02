import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.TimeUnit;

class AsyncExample {
    static CompletableFuture<Integer> loadValueAsync(boolean shouldFail) {
        return CompletableFuture.supplyAsync(() -> {
            if (shouldFail) throw new IllegalStateException("Load failed");
            return 42;
        }, CompletableFuture.delayedExecutor(10, TimeUnit.MILLISECONDS));
    }

    // Continuation-based equivalent; cleanup here does not throw.
    static CompletionStage<Integer> demonstrateAwaitWithFinally(boolean shouldFail) {
        CompletableFuture<Integer> pending = loadValueAsync(shouldFail);
        return pending.handle((value, error) -> {
            if (error != null) {
                System.out.println("CATCH: " + error.getMessage());
                return 0;
            }
            return value;
        }).whenComplete((value, error) -> {
            System.out.println("FINALLY: cleanup");
        }).thenApply(result -> {
            System.out.println("Continue: " + result);
            return result;
        });
    }
}
// Call with true for the shown rejection path, false for success.
