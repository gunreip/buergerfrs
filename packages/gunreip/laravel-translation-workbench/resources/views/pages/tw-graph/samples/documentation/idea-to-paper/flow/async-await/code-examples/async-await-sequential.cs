using System;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadValueAsync()
    {
        await Task.Delay(10);
        return 20;
    }

    static async Task<int> DoubleValueAsync(int value)
    {
        await Task.Delay(10);
        return value * 2;
    }

    static async Task<int> DemonstrateSequentialAwaits()
    {
        Task<int> first = LoadValueAsync();
        int value = await first;
        Task<int> second = DoubleValueAsync(value); // Starts after first completes.
        int result = await second;
        Console.WriteLine(result); // 40
        return result;
    }
}
