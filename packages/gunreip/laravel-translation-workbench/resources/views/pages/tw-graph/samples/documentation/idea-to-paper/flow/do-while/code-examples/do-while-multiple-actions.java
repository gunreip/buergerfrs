prepareOperation();
Result result;
do {
    result = performAction();
    recordResult(result);
} while (shouldRepeat(result));
showSummary();
