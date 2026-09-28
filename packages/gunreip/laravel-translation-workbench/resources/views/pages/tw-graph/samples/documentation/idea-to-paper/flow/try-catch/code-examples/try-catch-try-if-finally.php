<?php
try {
    if ($usePrimary) {
        performPrimary();
    } else {
        performSecondary();
    }
    recordSuccess();
} catch (Throwable $error) {
    handleFailure($error);
} finally {
    cleanup();
}
continueProcess();
