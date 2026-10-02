class FunctionExample {
    static int addOne(int value) {
        int result = value + 1;
        return result;
    }

    static int demonstrateCall() {
        int value = 41;
        int result = addOne(value);
        System.out.println(result); // 42, after addOne has returned.
        return result;
    }
}
