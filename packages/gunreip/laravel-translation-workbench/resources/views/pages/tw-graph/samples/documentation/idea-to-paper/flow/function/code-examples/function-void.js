function logMessage(message) {
    console.log(message);
    // Reaching the end returns undefined; the caller does not use it.
}

function demonstrateVoid() {
    console.log('before');
    const message = 'Processed item';
    logMessage(message);
    console.log('after');
}
