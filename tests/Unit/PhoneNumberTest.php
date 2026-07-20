<?php

declare(strict_types=1);

use App\Support\PhoneNumber;

it('normalizes a 10-digit US number in common formats', function (string $input) {
    expect(PhoneNumber::normalize($input))->toBe('4155550199');
})->with([
    '415-555-0199',
    '(415) 555-0199',
    '4155550199',
    '+14155550199',
    '14155550199',
    '+4155550199',
]);

it('strips a stray non-leading + instead of keeping it', function () {
    // Regression: a trailing '+' used to survive and produce "(415) 555-019+".
    // With only 9 real digits it is not a valid 10-digit number.
    expect(PhoneNumber::normalize('415-555-019+'))->toBeNull();
});

it('returns null for input that is not a 10-digit number', function (string $input) {
    expect(PhoneNumber::normalize($input))->toBeNull();
})->with([
    'abc',
    '555-0199',
    '',
]);

it('formats a normalizable number and rejects a malformed one', function () {
    expect(PhoneNumber::format('+1 (415) 555-0199'))->toBe('(415) 555-0199');
    expect(PhoneNumber::format('415-555-019+'))->toBeNull();
});
