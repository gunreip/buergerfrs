using System;
using System.Threading.Tasks;

record Outcome(string Status, int? Value, Exception? Error);

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

    static async Task<Outcome> Observe(Task<int> operation)
    {
        try {
            return new Outcome("fulfilled", await operation, null);
        } catch (Exception error) when (error is not OperationCanceledException) {
            return new Outcome("rejected", null, error);
        }
    }

    static async Task<Outcome[]> DemonstratePartialSuccess()
    {
        Task<int> first = LoadAAsync();
        Task<int> second = LoadBAsync();
        Outcome[] outcomes = await Task.WhenAll(Observe(first), Observe(second));
        foreach (Outcome outcome in outcomes) {
            Console.WriteLine(outcome.Status == "fulfilled"
                ? "Value: " + outcome.Value : "Error: " + outcome.Error!.Message);
        }
        return outcomes;
    }
}
// Errors become outcome records before WhenAll; cancellation remains separate.
