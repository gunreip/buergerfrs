using System;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadAAsync()
    {
        await Task.Delay(20);
        return 20;
    }

    static async Task<int> LoadBAsync()
    {
        await Task.Delay(10);
        return 40;
    }

    static async Task<int[]> DemonstrateConcurrentAwait()
    {
        Task<int> first = LoadAAsync();
        Task<int> second = LoadBAsync();
        int[] results = await Task.WhenAll(first, second);
        Console.WriteLine(string.Join(", ", results)); // 20, 40
        return results;
    }
}
// Success path only; fault/cancellation handling is a separate example.
