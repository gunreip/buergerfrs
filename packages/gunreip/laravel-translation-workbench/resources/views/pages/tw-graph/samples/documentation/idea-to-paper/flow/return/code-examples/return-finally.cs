// Inside a class; using System.Collections.Generic.
static int DoublePositive(int value, List<string> events)
{
    try
    {
        if (value > 0)
        {
            return value * 2;
        }
        return 0;
    }
    finally
    {
        events.Add("cleanup");
        value = 0; // Does not change the already evaluated int.
    }
}
