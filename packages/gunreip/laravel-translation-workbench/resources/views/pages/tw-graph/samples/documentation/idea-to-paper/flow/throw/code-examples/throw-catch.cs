// Inside a class; using System; using System.Collections.Generic.
static void DemonstrateThrow(List<string> events)
{
    try
    {
        events.Add("try");
        throw new ArgumentException("Invalid input");
        // events.Add("unreachable"); // Skipped; would produce an unreachable-code warning.
    }
    catch (ArgumentException error)
    {
        events.Add("caught");
    }
    events.Add("after");
}
