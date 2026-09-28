int attempt = 0;
const int maxAttempts = 3;
bool success;
do {
    attempt++;
    success = try_operation();
} while (!success && attempt < maxAttempts);
report_outcome(success, attempt);
