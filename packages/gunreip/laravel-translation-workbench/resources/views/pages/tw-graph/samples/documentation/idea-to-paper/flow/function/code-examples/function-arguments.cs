using System;

class FunctionExample
{
    static int Adjust(int value, int factor)
    {
        value = value + 1; // No ref/out parameter: caller value is unchanged.
        int result = value * factor;
        return result;
    }

    static void DemonstrateArguments()
    {
        int value = 20;
        int factor = 2;
        int result = Adjust(value, factor);
        Console.WriteLine($"value={value}; factor={factor}; result={result}");
        // value=20; factor=2; result=42
    }
}
