using System;
using System.Threading;
using System.Threading.Tasks;

class TransientException : Exception
{
    public TransientException(string message) : base(message) {}
}

class AsyncExample
{
    static async Task<int> Retry(
        Func<int, CancellationToken, Task<int>> operation,
        int maxAttempts, int delayMs, CancellationToken token)
    {
        if (maxAttempts < 1 || delayMs < 0)
            throw new ArgumentOutOfRangeException();
        for (int attempt = 1; ; attempt++) {
            token.ThrowIfCancellationRequested();
            try {
                int value = await operation(attempt, token);
                token.ThrowIfCancellationRequested();
                return value;
            } catch (Exception error) {
                token.ThrowIfCancellationRequested();
                if (error is not TransientException || attempt == maxAttempts) throw;
                await Task.Delay(delayMs, token);
            }
        }
    }

    static async Task<int> DemonstrateDeadline(CancellationToken callerToken)
    {
        using var combined = new CancellationTokenSource();
        int cause = 0; // First request wins: 1 = caller, 2 = deadline.
        using var registration = callerToken.Register(() => {
            if (Interlocked.CompareExchange(ref cause, 1, 0) == 0) combined.Cancel();
        });
        using var deadline = new Timer(_ => {
            if (Interlocked.CompareExchange(ref cause, 2, 0) == 0) combined.Cancel();
        }, null, 50, Timeout.Infinite);
        try {
            return await Retry(async (attempt, token) => {
                await Task.Delay(attempt == 1 ? 10 : 100, token);
                if (attempt == 1) throw new TransientException("Temporarily unavailable");
                return 42;
            }, 3, 20, combined.Token);
        } catch (OperationCanceledException) when (Volatile.Read(ref cause) == 2) {
            throw new TimeoutException("Total retry deadline exceeded");
        } catch (OperationCanceledException) when (Volatile.Read(ref cause) == 1) {
            throw new OperationCanceledException(callerToken);
        } finally {
            await deadline.DisposeAsync(); // Wait for any running timer callback.
        }
    }
}
// One 50ms timer covers all attempts and delays; the timer is never reset.
// Operations must cooperate with cancellation. Cleanup can extend past the deadline.
