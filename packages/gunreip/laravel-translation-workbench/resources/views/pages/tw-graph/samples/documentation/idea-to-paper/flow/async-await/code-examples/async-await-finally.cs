using System;
using System.Threading.Tasks;

class AsyncExample
{
    static async Task<int> LoadValueAsync(bool shouldFail)
    {
        await Task.Delay(10);
        if (shouldFail) throw new InvalidOperationException("Load failed");
        return 42;
    }

    static async Task<int> DemonstrateAwaitWithFinally(bool shouldFail = true)
    {
        int result;
        try {
            Task<int> pending = LoadValueAsync(shouldFail);
            result = await pending;
        } catch (InvalidOperationException error) {
            Console.WriteLine("CATCH: " + error.Message);
            result = 0;
        } finally {
            Console.WriteLine("FINALLY: cleanup");
        }
        Console.WriteLine("Continue: " + result);
        return result;
    }
}
