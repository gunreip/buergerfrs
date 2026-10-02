<?php
function addOne(int $value): int
{
    $result = $value + 1;
    return $result;
}

function demonstrateMultipleCalls(): array
{
    $first = addOne(10);  // Call site 1 resumes here with 11.
    $second = addOne(40); // Call site 2 resumes here with 41.
    echo "first=$first; second=$second";
    return [$first, $second];
}
