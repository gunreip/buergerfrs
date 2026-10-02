using System;
using System.Collections.Generic;
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
        throw new InvalidOperationException("Load B failed");
    }

    static async Task ObserveFailure(Task<int> operation)
    {
        try { await operation; }
        catch (Exception) { /* Observe errors from unfinished candidates. */ }
    }

    static async Task<int> FirstCompletion()
    {
        Task<int> a = LoadAAsync();
        Task<int> b = LoadBAsync();
        _ = ObserveFailure(a);
        _ = ObserveFailure(b);
        Task<int> winner = await Task.WhenAny(a, b);
        return await winner; // Propagates the winning task's error to the caller.
    }

    static async Task<int> FirstSuccess()
    {
        var pending = new List<Task<int>> { LoadAAsync(), LoadBAsync() };
        foreach (var candidate in pending) _ = ObserveFailure(candidate);
        var errors = new List<Exception>();
        while (pending.Count > 0) {
            Task<int> finished = await Task.WhenAny(pending);
            pending.Remove(finished);
            try { return await finished; }
            catch (Exception error) { errors.Add(error); }
        }
        throw new AggregateException(errors);
    }
}
// Invoke separately; remaining work continues. Cancellation is not modelled.
