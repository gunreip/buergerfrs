class FunctionExample {
    static int addOne(int value) {
        int result = value + 1;
        return result; // 21: resumes doubleAdjusted().
    }

    static int doubleAdjusted(int value) {
        int adjusted = addOne(value);
        int result = adjusted * 2;
        return result; // 42: resumes the original caller.
    }

    static int demonstrateNested() {
        int value = 20;
        int result = doubleAdjusted(value);
        System.out.println(result);
        return result;
    }
}
