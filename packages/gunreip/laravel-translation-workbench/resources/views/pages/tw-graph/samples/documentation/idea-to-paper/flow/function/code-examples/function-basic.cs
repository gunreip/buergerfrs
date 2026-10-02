using System;

class FunctionExample
{
    static int AddOne(int value)
    {
        int result = value + 1;
        return result;
    }

    static int DemonstrateCall()
    {
        int value = 41;
        int result = AddOne(value);
        Console.WriteLine(result); // 42, after AddOne has returned.
        return result;
    }
}
