using System.Collections.Generic;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> TransformAsync(int item)
    {
        await Task.Delay(10);
        return item + 1;
    }

    static async Task<List<int>> ProcessItems(IEnumerable<int> items)
    {
        var results = new List<int>();
        foreach (int item in items) {
            Task<int> pending = TransformAsync(item);
            int result = await pending;
            results.Add(result);
        }
        return results;
    }
}
// ProcessItems(new[] {10, 20, 30}) completes with [11, 21, 31].
// Empty input starts no operation; an exception stops the sequence.
