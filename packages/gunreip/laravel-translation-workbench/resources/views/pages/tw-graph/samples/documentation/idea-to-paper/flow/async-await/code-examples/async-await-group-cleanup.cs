using System;
using System.Runtime.ExceptionServices;
using System.Threading;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadA(CancellationToken token)
    {
        try {
            await Task.Delay(10, token);
            throw new InvalidOperationException("A failed");
        } finally { await Task.Delay(5); }
    }

    static async Task<int> LoadB(CancellationToken token)
    {
        try { await Task.Delay(100, token); return 20; }
        finally { await Task.Delay(5); }
    }

    static async Task<int[]> RunGroup()
    {
        using var cancellation = new CancellationTokenSource();
        Exception? original = null;
        async Task<int> Observe(Func<CancellationToken, Task<int>> operation)
        {
            try { return await operation(cancellation.Token); }
            catch (Exception error) {
                if (Interlocked.CompareExchange(ref original, error, null) is null)
                    cancellation.Cancel();
                throw;
            }
        }
        Task<int> a = Observe(LoadA);
        Task<int> b = Observe(LoadB);
        try { return await Task.WhenAll(a, b); }
        catch {
            // WhenAll has now waited for both tasks, including their FINALLY blocks.
            ExceptionDispatchInfo.Capture(original!).Throw();
            throw;
        }
    }
}
// Cleanup does not use the cancelled token. Caller handles the original A error.
