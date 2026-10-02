import java.util.concurrent.*;

class TransientException extends RuntimeException {
    TransientException(String message) { super(message); }
}

class AsyncExample {
    // A dedicated future is completed exceptionally to request cancellation.
    static CompletableFuture<Void> delay(long ms, ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        if (cancelled.isDone()) return cancelled;
        var timer = new CompletableFuture<Void>();
        var scheduled = scheduler.schedule(() -> timer.complete(null), ms, TimeUnit.MILLISECONDS);
        var wait = CompletableFuture.anyOf(timer, cancelled).thenApply(ignored -> (Void) null);
        wait.whenComplete((ignored, error) -> scheduled.cancel(false));
        return wait;
    }

    static CompletableFuture<Integer> operation(int attempt, ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        return delay(10, scheduler, cancelled).thenApply(ignored -> {
            if (attempt == 1) throw new TransientException("Temporarily unavailable");
            return 42;
        });
    }

    static Throwable cause(Throwable error) {
        while (error instanceof CompletionException && error.getCause() != null)
            error = error.getCause();
        return error;
    }

    static CompletableFuture<Integer> attempt(int number, int maxAttempts, long delayMs,
            ScheduledExecutorService scheduler, CompletableFuture<Void> cancelled) {
        if (cancelled.isDone()) return cancelled.thenApply(ignored -> 0);
        return operation(number, scheduler, cancelled).handle((value, error) -> {
            if (cancelled.isDone()) return cancelled.thenApply(ignored -> 0);
            if (error == null) return CompletableFuture.completedFuture(value);
            Throwable failure = cause(error);
            if (!(failure instanceof TransientException) || number == maxAttempts)
                return CompletableFuture.<Integer>failedFuture(failure);
            return delay(delayMs, scheduler, cancelled).thenCompose(ignored ->
                attempt(number + 1, maxAttempts, delayMs, scheduler, cancelled));
        }).thenCompose(next -> next);
    }

    static CompletableFuture<Integer> demonstrateRetry(ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        return attempt(1, 3, 20, scheduler, cancelled);
    }
}
// Caller owns scheduler lifetime; shut it down after observing completion.
// To cancel: cancelled.completeExceptionally(new CancellationException("Cancelled"));
// Never complete this dedicated cancellation future normally.
// This timer-based sample cooperatively cancels both the operation and retry delay.
