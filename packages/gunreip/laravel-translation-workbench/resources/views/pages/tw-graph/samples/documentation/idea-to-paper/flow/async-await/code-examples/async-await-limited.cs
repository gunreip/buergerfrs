using System;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;

record Outcome(int? Value, Exception? Error);

class AsyncExample
{
    static async Task<int> LoadAsync(string item)
    {
        await Task.Delay(item == "A" ? 60 : 10);
        return item == "A" ? 10 : item == "B" ? 20 : 30;
    }

    static async Task<Outcome[]> MapLimited(
        string[] items, int limit, Func<string, Task<int>> operation)
    {
        if (limit < 1) throw new ArgumentOutOfRangeException(nameof(limit));
        var outcomes = new Outcome[items.Length];
        int next = -1;
        async Task Worker()
        {
            while (true) {
                int index = Interlocked.Increment(ref next);
                if (index >= items.Length) return;
                try { outcomes[index] = new Outcome(await operation(items[index]), null); }
                catch (Exception error) { outcomes[index] = new Outcome(null, error); }
            }
        }
        var workers = Enumerable.Range(0, Math.Min(limit, items.Length))
            .Select(_ => Worker()).ToArray();
        await Task.WhenAll(workers);
        return outcomes;
    }

    static Task<Outcome[]> DemonstrateLimitedConcurrency() =>
        MapLimited(new[] { "A", "B", "C" }, 2, LoadAsync);
}
// All outcomes are collected. Cancellation policy is outside this example.
