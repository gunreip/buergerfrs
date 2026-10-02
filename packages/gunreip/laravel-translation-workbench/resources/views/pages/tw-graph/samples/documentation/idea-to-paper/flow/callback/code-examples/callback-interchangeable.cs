using System;

class CallbackExample
{
    static int AddOne(int value)
    {
        return value + 1;
    }

    static int DoubleValue(int value)
    {
        return value * 2;
    }

    static int Apply(int value, Func<int, int> callback)
    {
        int result = callback(value);
        return result;
    }

    static void DemonstrateCallbacks()
    {
        int first = Apply(20, AddOne);
        int second = Apply(20, DoubleValue);
        Console.WriteLine($"first={first}; second={second}"); // 21 and 40
    }
}
