prepareOperation();
let result;
do {
    result = performAction();
} while (shouldRepeat(result));
showSummary();
