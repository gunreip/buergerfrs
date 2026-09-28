prepare_operation();
Result result;
do {
    result = perform_action();
    record_result(result);
} while (should_repeat(result));
show_summary();
