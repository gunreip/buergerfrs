using System;
using System.Threading;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadValueAsync(CancellationToken token)
    {
        await Task.Delay(100, token); // Observes cancellation.
        return 42;
    }

    static async Task<string> DemonstrateCancellation(CancellationToken token)
    {
        string outcome;
        try {
            int value = await LoadValueAsync(token);
            outcome = "completed: " + value;
        } catch (OperationCanceledException error)
            when (token.IsCancellationRequested && error.CancellationToken == token) {
            Console.WriteLine("CATCH: cancelled");
            outcome = "cancelled";
        } finally {
            Console.WriteLine("FINALLY: cleanup");
        }
        return outcome;
    }

    static async Task<string> RunExample()
    {
        using var source = new CancellationTokenSource();
        Task<string> pending = DemonstrateCancellation(source.Token);
        source.Cancel();
        return await pending;
    }
}
