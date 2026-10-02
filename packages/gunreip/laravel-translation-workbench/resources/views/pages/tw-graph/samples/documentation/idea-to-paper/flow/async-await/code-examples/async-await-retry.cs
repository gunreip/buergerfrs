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

    static Task<int> DemonstrateRetry(CancellationToken token) =>
        Retry(async (attempt, currentToken) => {
            await Task.Delay(10, currentToken);
            if (attempt == 1) throw new TransientException("Temporarily unavailable");
            return 42;
        }, 3, 20, token);
}
// A caller-owned CancellationTokenSource can interrupt the operation or delay.
// Only retry operations that are safe to repeat; the initial call counts as attempt 1.
