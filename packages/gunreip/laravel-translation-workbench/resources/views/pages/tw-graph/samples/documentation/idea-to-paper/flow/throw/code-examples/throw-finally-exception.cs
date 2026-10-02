// Inside a class; using System; using System.Collections.Generic.
static void FailAndCleanup(List<string> events)
{
    ArgumentException original = null;
    try
    {
        events.Add("original");
        throw new ArgumentException("Original failure");
    }
    catch (ArgumentException error)
    {
        original = error;
        throw;
    }
    finally
    {
        events.Add("cleanup");
        throw new InvalidOperationException("Cleanup failed", original);
    }
}

static InvalidOperationException DemonstrateReplacement(List<string> events)
{
    InvalidOperationException caught = null;
    try
    {
        events.Add("outer-try");
        FailAndCleanup(events);
        events.Add("unreachable-after-call");
    }
    catch (InvalidOperationException error)
    {
        events.Add("outer-caught");
        caught = error; // InnerException is the original ArgumentException.
    }
    events.Add("after");
    return caught;
}
