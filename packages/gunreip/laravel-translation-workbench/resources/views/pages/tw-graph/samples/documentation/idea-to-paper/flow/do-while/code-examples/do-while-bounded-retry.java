int attempt = 0;
final int maxAttempts = 3;
boolean success;
do {
    attempt++;
    success = tryOperation();
} while (!success && attempt < maxAttempts);
reportOutcome(success, attempt);
