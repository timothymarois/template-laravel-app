# Vue Utilities

## Table of Contents

- [Format Utilities](#format-utilities)
  - [formatBytes](#formatbytes)
  - [formatDate](#formatdate)
  - [formatDatetime](#formatdatetime)
  - [formatYmdDate](#formatymddate)
  - [formatNumber](#formatnumber)
  - [formatPercentage](#formatpercentage)
  - [formatSlug](#formatslug)
  - [formatValidURL](#formatvalidurl)
  - [formatUSPhoneNumber](#formatusphonenumber)
- [Validation Utilities](#validation-utilities)
  - [isEmpty](#isempty)
  - [isNumeric](#isnumeric)
  - [isValidEmail](#isvalidemail)
  - [isValidURL](#isvalidurl)
- [Other Utilities](#other-utilities)
  - [isClient](#isclient)
  - [getRandomItem](#getrandomitem)
  - [roundTo](#roundto)
  - [isPageActive](#ispageactive)

## Format Utilities

Utilities for formatting various data types into human-readable strings.

### formatBytes

Converts a number of bytes into a human-readable file size string.

**Parameters:**
- `bytes` (number): Number of bytes
- `decimals` (number, optional): Number of decimal places (default: 2)

**Returns:** The formatted size string (e.g. '1.24 MB')

**Example:**
```typescript
import { formatBytes } from '@atlas/ui';

formatBytes(1024);        // '1.00 KB'
formatBytes(1536, 1);     // '1.5 KB'
formatBytes(1073741824);  // '1.00 GB'
formatBytes(0);           // '0 Bytes'
```

### formatDate

Formats a date string or Date object into a localized date string.

**Parameters:**
- `utcDatetime` (string | Date): The date to format
- `userTimezone` (string, optional): User's timezone (default: 'UTC')
- `locale` (string, optional): Locale string (default: 'en-US')

**Returns:** The formatted date string

**Example:**
```typescript
import { formatDate } from '@atlas/ui';

formatDate('2023-01-15T12:00:00');                  // '1/15/2023'
formatDate('2023-01-15T12:00:00', 'America/New_York'); // '1/15/2023'
formatDate(new Date(2023, 0, 15), 'Europe/London', 'en-GB'); // '15/01/2023'
```

### formatDatetime

Formats a date string or Date object into a localized date and time string.

**Parameters:**
- `utcDatetime` (string | Date): The date to format
- `userTimezone` (string, optional): User's timezone (default: 'UTC')
- `locale` (string, optional): Locale string (default: 'en-US')

**Returns:** The formatted date and time string

**Example:**
```typescript
import { formatDatetime } from '@atlas/ui';

formatDatetime('2023-01-15T12:00:00');                  // '1/15/2023, 12:00 PM'
formatDatetime('2023-01-15T12:00:00', 'America/New_York'); // '1/15/2023, 7:00 AM'
formatDatetime(new Date(2023, 0, 15, 12), 'Europe/London', 'en-GB'); // '15/01/2023, 12:00 pm'
```

### formatYmdDate

Converts a `YYYY-MM-DD` date string into `MM/DD/YYYY` format.

**Parameters:**
- `dateString` (string): Date string in `YYYY-MM-DD` format

**Returns:** The formatted date string

**Example:**
```typescript
import { formatYmdDate } from '@atlas/ui';

formatYmdDate('2023-12-05'); // '12/05/2023'
formatYmdDate('2023-01-01'); // '01/01/2023'
```

### formatNumber

Formats a number with thousands separators and optional decimal places.

**Parameters:**
- `n` (string | number): The number to format
- `decimals` (number, optional): Number of decimal places (default: 0)

**Returns:** The formatted number string

**Example:**
```typescript
import { formatNumber } from '@atlas/ui';

formatNumber(1000);      // '1,000'
formatNumber(1234.56, 2); // '1,234.56'
formatNumber('5000');     // '5,000'
formatNumber('abc');      // 'Invalid'
```

### formatPercentage

Formats a numeric decimal into a percentage string.

**Parameters:**
- `value` (number): The decimal value (e.g. 0.25 = 25%)
- `decimals` (number, optional): Number of decimal places (default: 0)

**Returns:** The formatted percentage string

**Example:**
```typescript
import { formatPercentage } from '@atlas/ui';

formatPercentage(0.25);     // '25%'
formatPercentage(0.3333, 2); // '33.33%'
formatPercentage(1);         // '100%'
formatPercentage(0);         // '0%'
```

### formatSlug

Converts a string into a safe, lowercase slug.

**Parameters:**
- `str` (string): The input string

**Returns:** A hyphenated slug (e.g. 'my-post-title')

**Example:**
```typescript
import { formatSlug } from '@atlas/ui';

formatSlug('Hello World');      // 'hello-world'
formatSlug('My Blog Post!');    // 'my-blog-post'
formatSlug('Café & Résumé');    // 'cafe-resume'
formatSlug('   Multiple   Spaces  '); // 'multiple-spaces'
```

### formatValidURL

Normalizes and ensures a URL includes a valid protocol.

**Parameters:**
- `url` (string): Input string (e.g. 'google.com')
- `https` (boolean, optional): Force HTTPS (default: false)

**Returns:** A valid HTTP(S) URL (e.g. 'https://google.com')

**Example:**
```typescript
import { formatValidURL } from '@atlas/ui';

formatValidURL('google.com');                // 'http://google.com'
formatValidURL('google.com', true);         // 'https://google.com'
formatValidURL('http://example.com');       // 'http://example.com'
formatValidURL('http://example.com', true); // 'https://example.com'
```

### formatUSPhoneNumber

Formats a US-based phone number from 10 digits into `(999) 999-9999` format. If the
input includes a leading `1` (e.g. `19995551234`), the extra digit is ignored. An
optional flag can prefix the result with `+1`.

**Parameters:**
- `phoneNumber` (string | number): The phone number to format (10 digits or 11 digits starting with `1`)
- `withCountryCode` (boolean, optional): Prefix the formatted number with `+1` (default: `false`)

**Returns:** The formatted phone number string (e.g. `(123) 456-7890` or `+1 (123) 456-7890`)

**Example:**
```typescript
import { formatUSPhoneNumber } from '@atlas/ui';

formatUSPhoneNumber('1234567890'); // '(123) 456-7890'
formatUSPhoneNumber(9876543210, true); // '+1 (987) 654-3210'
formatUSPhoneNumber('19998887777'); // '(999) 888-7777'
```

## Validation Utilities

Utilities for validating data types and formats.

### isEmpty

Checks if a value is empty (null, undefined, empty string, empty array, or empty object).

**Parameters:**
- `value` (any): The value to check

**Returns:** Boolean indicating if the value is empty

**Example:**
```typescript
import { isEmpty } from '@atlas/ui';

isEmpty('');        // true
isEmpty(null);       // true
isEmpty(undefined);  // true
isEmpty([]);         // true
isEmpty({});         // true
isEmpty('text');     // false
isEmpty([1, 2, 3]);  // false
isEmpty({ key: 'value' }); // false
```

### isNumeric

Checks if a value is a valid number or numeric string.

**Parameters:**
- `value` (any): The value to check

**Returns:** Boolean indicating if the value is numeric

**Example:**
```typescript
import { isNumeric } from '@atlas/ui';

isNumeric(123);        // true
isNumeric('123');       // true
isNumeric('123.45');    // true
isNumeric('-123');      // true
isNumeric('abc');       // false
isNumeric('123abc');    // false
isNumeric(null);        // false
```

### isValidEmail

Checks if a string is a valid email address format.

**Parameters:**
- `email` (string): The email string to validate

**Returns:** Boolean indicating if the string is a valid email format

**Example:**
```typescript
import { isValidEmail } from '@atlas/ui';

isValidEmail('user@example.com');      // true
isValidEmail('name.surname@domain.co'); // true
isValidEmail('email@subdomain.domain.com'); // true
isValidEmail('plainaddress');          // false
isValidEmail('email@domain');           // false
isValidEmail('@domain.com');            // false
```

### isValidURL

Checks if a string is a valid URL format.

**Parameters:**
- `url` (string): The URL string to validate

**Returns:** Boolean indicating if the string is a valid URL format

**Example:**
```typescript
import { isValidURL } from '@atlas/ui';

isValidURL('https://google.com');         // true
isValidURL('http://subdomain.domain.co.uk'); // true
isValidURL('https://domain.com/path?query=123'); // true
isValidURL('domain.com');                 // false (no protocol)
isValidURL('https://');                   // false (no domain)
isValidURL('not a url');                  // false
```

## Other Utilities

### isClient

Detects if the code is running in a client/browser environment (as opposed to server-side).

**Parameters:** None

**Returns:** Boolean indicating if running in a browser

**Example:**
```typescript
import { isClient } from '@atlas/ui';

// Only execute browser-specific code when in the browser
if (isClient) {
  window.addEventListener('resize', handleResize);
}
```

### getRandomItem

Returns a random element from an array.

**Parameters:**
- `items` (any[]): Array of items

**Returns:** A random element from the array or `undefined` for empty arrays

**Example:**
```typescript
import { getRandomItem } from '@atlas/ui';

const numbers = [1, 2, 3, 4];
getRandomItem(numbers); // 1 | 2 | 3 | 4
```

### roundTo

Rounds a number to a specified number of decimal places.

**Parameters:**
- `num` (number): The number to round
- `decimals` (number, optional): Decimal places (default: 2)

**Returns:** The rounded number

**Example:**
```typescript
import { roundTo } from '@atlas/ui';

roundTo(1.005, 2); // 1.01
roundTo(123.456, 1); // 123.5
```

### isPageActive

Determines whether the current Inertia page URL matches a given path. This utility returns `false` during server-side rendering to avoid accessing browser-only globals.

**Parameters:**
- `itemPath` (string): The path to compare against the current page URL.
- `itemParent` (string, optional): Parent path that overrides `itemPath`.
- `eq` (boolean, optional): If true, requires an exact match; otherwise checks that the current path starts with the route path.

**Returns:** Boolean indicating whether the current page is active.

**Example:**
```typescript
import { isPageActive } from '@atlas/ui';

isPageActive('/users'); // true when current URL begins with '/users'
isPageActive('/users', undefined, true); // true only when URL is exactly '/users'
isPageActive('/profile', '/users'); // checks against '/users'
```
