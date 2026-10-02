using System;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadValueAsync()
    {
        await Task.Delay(10);
        return 42;
    }

    static async Task<int> DemonstrateAwait()
    {
        Task<int> pending = LoadValueAsync();
        int result = await pending; // Suspends if the task is incomplete.
        Console.WriteLine(result); // 42
        return result;
    }
}
