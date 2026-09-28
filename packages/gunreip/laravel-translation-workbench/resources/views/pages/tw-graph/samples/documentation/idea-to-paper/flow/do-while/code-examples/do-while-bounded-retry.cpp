int attempt = 0;
const int maxAttempts = 3;
bool success;
do {
    attempt++;
    success = tryOperation();
} while (!success && attempt < maxAttempts);
reportOutcome(success, attempt);
