int attempt = 0;
const int maxAttempts = 3;
bool success;
do {
    attempt++;
    success = TryOperation();
} while (!success && attempt < maxAttempts);
ReportOutcome(success, attempt);
