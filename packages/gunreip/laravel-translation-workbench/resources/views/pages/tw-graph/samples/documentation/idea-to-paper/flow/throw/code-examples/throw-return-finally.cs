// Inside a class; using System; using System.Collections.Generic.
// C# forbids RETURN leaving FINALLY: compiler error CS0157.
// This alternative explicitly handles the error in CATCH, then returns.
// It produces the same fallback value, but is NOT the graph's control flow.
static int FailWithExplicitFallback(List<string> events)
{
    try
    {
        events.Add("original");
        throw new ArgumentException("Original failure");
    }
    catch (ArgumentException)
    {
        events.Add("handled");
    }
    finally
    {
        events.Add("cleanup");
        // return 7; // Illegal: CS0157.
    }
    return 7;
}
