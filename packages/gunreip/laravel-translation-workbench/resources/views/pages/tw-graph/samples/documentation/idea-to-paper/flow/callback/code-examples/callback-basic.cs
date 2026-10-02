using System;

class CallbackExample
{
    static int AddOne(int value)
    {
        return value + 1;
    }

    static int Apply(int value, Func<int, int> callback)
    {
        int result = callback(value);
        return result;
    }

    static void DemonstrateCallback()
    {
        int result = Apply(41, AddOne); // Method group becomes a delegate.
        Console.WriteLine(result); // 42
    }
}
