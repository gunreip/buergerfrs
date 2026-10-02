using System;
using System.Threading;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadValueAsync(CancellationToken token)
    {
        await Task.Delay(100, token);
        return 42;
    }

    static async Task<string> DemonstrateTimeout(int timeoutMs = 20)
    {
        using var source = new CancellationTokenSource();
        source.CancelAfter(timeoutMs);
        string outcome;
        try {
            int result = await LoadValueAsync(source.Token);
            outcome = "completed: " + result;
        } catch (OperationCanceledException error)
            when (source.IsCancellationRequested && error.CancellationToken == source.Token) {
            Console.WriteLine("CATCH: timeout");
            outcome = "timeout";
        } finally {
            source.CancelAfter(Timeout.Infinite); // Disable a still-pending deadline.
            Console.WriteLine("FINALLY: deadline cleared");
        }
        return outcome;
    }
}
// The timeout source is dedicated to this deadline; no user cancellation is mixed in.
// Cancellation is cooperative, not a guaranteed maximum completion time.
