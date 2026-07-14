<?php

use App\Models\Person;

test('person can be marked as test data', function (): void {
    $person = Person::query()->create([
        'person_number' => 'TEST-001',
        'is_test_data' => true,
        'first_name' => 'Test',
        'last_name' => 'Person',
    ]);

    expect($person->refresh()->is_test_data)->toBeTrue();
});
