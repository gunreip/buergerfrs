prepare_operation();
Result result;
do {
    result = perform_action();
} while (should_repeat(result));
show_summary();
