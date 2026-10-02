// Inside a class; using System; using System.Collections.Generic.
static int ComputeAndCleanup(List<string> events)
{
    try
    {
        events.Add("pending-return");
        return 42;
    }
    finally
    {
        events.Add("cleanup");
        throw new InvalidOperationException("Cleanup failed");
    }
}

static int? DemonstrateFinallyThrow(List<string> events)
{
    int? result = null;
    try
    {
        events.Add("outer-try");
        result = ComputeAndCleanup(events);
        events.Add("unreachable-after-call");
    }
    catch (InvalidOperationException)
    {
        events.Add("outer-caught");
    }
    events.Add("after");
    return result; // Still null.
}
