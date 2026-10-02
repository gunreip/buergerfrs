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

    static class TransientException extends RuntimeException {
        TransientException(String message) { super(message); }
    }

    static CompletableFuture<Integer> attempt(int number, ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        if (cancelled.isDone()) return cancelled.thenApply(ignored -> 0);
        return delay(number == 1 ? 10 : 100, scheduler, cancelled).thenApply(ignored -> {
            if (number == 1) throw new TransientException("Temporarily unavailable");
            return 42;
        }).handle((value, error) -> {
            if (cancelled.isDone()) return cancelled.thenApply(ignored -> 0);
            if (error == null) return CompletableFuture.completedFuture(value);
            Throwable failure = cause(error);
            if (!(failure instanceof TransientException) || number == 3)
                return CompletableFuture.<Integer>failedFuture(failure);
            return delay(20, scheduler, cancelled).thenCompose(ignored ->
                attempt(number + 1, scheduler, cancelled));
        }).thenCompose(next -> next);
    }

    static CompletableFuture<Integer> demonstrateDeadline(ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        var deadline = scheduler.schedule(() -> cancelled.completeExceptionally(
            new TimeoutException("Total retry deadline exceeded")), 50, TimeUnit.MILLISECONDS);
        return attempt(1, scheduler, cancelled)
            .whenComplete((value, error) -> deadline.cancel(false));
    }
}
// Use a FRESH cancellation future for each invocation; never complete it normally.
// Caller cancellation completes that future exceptionally with CancellationException.
// The first cancellation reason wins. One deadline covers every attempt and delay.
// Caller owns scheduler lifetime. Timer delivery and cooperative cleanup may run late.
