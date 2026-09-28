let attempt = 0;
const maxAttempts = 3;
let success;
do {
    attempt++;
    success = tryOperation();
} while (!success && attempt < maxAttempts);
reportOutcome(success, attempt);
