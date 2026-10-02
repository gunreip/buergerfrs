using System;

class FunctionExample
{
    static int AddOne(int value)
    {
        int result = value + 1;
        return result; // 21: resumes DoubleAdjusted().
    }

    static int DoubleAdjusted(int value)
    {
        int adjusted = AddOne(value);
        int result = adjusted * 2;
        return result; // 42: resumes the original caller.
    }

    static int DemonstrateNested()
    {
        int value = 20;
        int result = DoubleAdjusted(value);
        Console.WriteLine(result);
        return result;
    }
}
