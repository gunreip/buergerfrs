using System.Collections.Generic;
using System.Runtime.CompilerServices;
using System.Threading;
using System.Threading.Tasks;

class AsyncExample
{
    static async IAsyncEnumerable<int> Values(
        [EnumeratorCancellation] CancellationToken token = default)
    {
        try {
            foreach (int value in new[] { 10, 20, 30 }) {
                await Task.Delay(10, token);
                yield return value;
            }
        } finally {
            await Task.Delay(5); // Release resources even when the token is cancelled.
        }
    }

    static async Task<List<int>> ConsumeStream(CancellationToken token, int take = 2)
    {
        var result = new List<int>();
        await foreach (int value in Values(token)) {
            result.Add(value);
            if (result.Count >= take) break;
        }
        return result; // DisposeAsync and producer FINALLY have finished.
    }
}
// take: int.MaxValue consumes to exhaustion. BREAK avoids requesting the third item.
