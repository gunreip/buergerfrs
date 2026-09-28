prepareOperation();
Result result;
do {
    result = performAction();
} while (shouldRepeat(result));
showSummary();
