function failWithCleanup() {
    try {
        console.log('original');
        throw new TypeError('Original failure');
    } finally {
        console.log('cleanup');
    }
}

function demonstrateUnhandled() {
    console.log('call');
    failWithCleanup(); // The caller/host receives the escaping exception.
    console.log('unreachable-after-call');
}
