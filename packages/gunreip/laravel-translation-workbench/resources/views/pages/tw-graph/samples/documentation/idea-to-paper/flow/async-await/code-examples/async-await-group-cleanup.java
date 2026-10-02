import java.util.*;
import java.util.concurrent.*;
import java.util.concurrent.atomic.AtomicReference;

class AsyncExample {
    static CompletableFuture<Void> delay(long ms, ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        if (cancelled.isDone()) return cancelled;
        var timer = new CompletableFuture<Void>();
        var scheduled = scheduler.schedule(() -> timer.complete(null), ms, TimeUnit.MILLISECONDS);
        var wait = CompletableFuture.anyOf(timer, cancelled).thenApply(ignored -> (Void) null);
        wait.whenComplete((ignored, error) -> scheduled.cancel(false));
        return wait;
    }

    static Throwable cause(Throwable error) {
        while (error instanceof CompletionException && error.getCause() != null)
            error = error.getCause();
        return error;
    }

    static <T> CompletableFuture<T> withCleanup(CompletableFuture<T> operation,
            ScheduledExecutorService scheduler) {
        return operation.handle((value, error) ->
            delay(5, scheduler, new CompletableFuture<>()).thenCompose(ignored ->
                error == null ? CompletableFuture.completedFuture(value)
                    : CompletableFuture.<T>failedFuture(cause(error))))
            .thenCompose(next -> next);
    }

    static CompletableFuture<Integer> observe(CompletableFuture<Integer> task,
            AtomicReference<Throwable> firstError, CompletableFuture<Void> cancelled) {
        return task.whenComplete((value, error) -> {
            if (error != null && firstError.compareAndSet(null, cause(error)))
                cancelled.completeExceptionally(firstError.get());
        });
    }

    static CompletableFuture<List<Integer>> runGroup(ScheduledExecutorService scheduler) {
        var cancelled = new CompletableFuture<Void>();
        var firstError = new AtomicReference<Throwable>();
        var a = observe(withCleanup(delay(10, scheduler, cancelled).<Integer>thenApply(ignored -> {
            throw new IllegalStateException("A failed");
        }), scheduler), firstError, cancelled);
        var b = observe(withCleanup(delay(100, scheduler, cancelled).thenApply(ignored -> 20),
            scheduler), firstError, cancelled);
        return CompletableFuture.allOf(a, b).handle((ignored, error) -> {
            if (firstError.get() != null) throw new CompletionException(firstError.get());
            return List.of(a.join(), b.join()); // Both tasks have settled; no blocking here.
        });
    }
}
// Caller owns scheduler; shut it down after observing the returned future.
// withCleanup waits for release before the task settles. Cancellation is cooperative.
