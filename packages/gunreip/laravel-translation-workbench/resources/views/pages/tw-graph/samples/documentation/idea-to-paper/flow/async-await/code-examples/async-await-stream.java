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

    static class Cursor {
        private final ScheduledExecutorService scheduler;
        private final CompletableFuture<Void> cancelled;
        private final List<Integer> items = List.of(10, 20, 30);
        private int index;
        private CompletableFuture<Void> closing;

        Cursor(ScheduledExecutorService scheduler, CompletableFuture<Void> cancelled) {
            this.scheduler = scheduler;
            this.cancelled = cancelled;
        }
        CompletableFuture<Optional<Integer>> next() {
            if (closing != null) return CompletableFuture.failedFuture(
                new IllegalStateException("Source closed"));
            if (cancelled.isDone()) return cancelled.thenApply(ignored -> Optional.empty());
            if (index == items.size()) return CompletableFuture.completedFuture(Optional.empty());
            int value = items.get(index++);
            return delay(10, scheduler, cancelled).thenApply(ignored -> Optional.of(value));
        }
        CompletableFuture<Void> close() {
            if (closing == null) closing = delay(5, scheduler, new CompletableFuture<>());
            return closing;
        }
    }

    static CompletableFuture<List<Integer>> read(Cursor cursor, List<Integer> result, int take) {
        return cursor.next().thenCompose(item -> {
            if (item.isEmpty()) return CompletableFuture.completedFuture(result);
            result.add(item.get());
            if (result.size() >= take) return CompletableFuture.completedFuture(result);
            return read(cursor, result, take);
        });
    }

    static CompletableFuture<List<Integer>> consumeStream(ScheduledExecutorService scheduler,
            CompletableFuture<Void> cancelled) {
        var cursor = new Cursor(scheduler, cancelled);
        return read(cursor, new ArrayList<>(), 2).handle((result, error) ->
            cursor.close().thenCompose(ignored -> error == null
                ? CompletableFuture.completedFuture(List.copyOf(result))
                : CompletableFuture.<List<Integer>>failedFuture(cause(error))))
            .thenCompose(next -> next);
    }
}
// Explicit async cursor: Java has no await-foreach statement here.
// Only one read is pending at a time. Close is awaited on success, early exit or failure.
// Caller cancellation: cancelled.completeExceptionally(new CancellationException());
// Caller owns scheduler and closes it after observing completion.
