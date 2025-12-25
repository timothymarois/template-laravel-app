<template>
    <LayoutApp
        title="Components - Input Masks"
        pageTitle="Input"
        :pageTabs="pageTabs"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Number Formatting -->
            <Card>
                <CardHeader>
                    <CardTitle>Number Formatting</CardTitle>
                    <CardDescription>Format numbers with thousand separators while keeping raw numeric values</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Integer (no decimals)</div>
                            <Input v-model="numberInteger" :formatter="numberFormatter()" placeholder="Enter amount..." fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ numberInteger || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With 2 Decimals</div>
                            <Input v-model="numberDecimals" :formatter="numberFormatter(2)" placeholder="Enter price..." fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ numberDecimals || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Positive Only</div>
                            <Input v-model="numberPositive" :formatter="numberFormatter(0, false)" placeholder="Positive numbers..." fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ numberPositive || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Currency -->
            <Card>
                <CardHeader>
                    <CardTitle>Currency Formatting</CardTitle>
                    <CardDescription>Format currency values with thousand separators and 2 decimal places</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Price</div>
                            <div class="flex items-center gap-2">
                                <span class="text-muted-foreground">$</span>
                                <Input v-model="currencyPrice" :formatter="currencyFormatter()" placeholder="0.00" fluid />
                            </div>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ currencyPrice || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Negative</div>
                            <div class="flex items-center gap-2">
                                <span class="text-muted-foreground">$</span>
                                <Input v-model="currencyNegative" :formatter="currencyFormatter(true)" placeholder="0.00" fluid />
                            </div>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ currencyNegative || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Percentage</div>
                            <Input v-model="percentage" :formatter="percentageFormatter(2)" placeholder="0.00%" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ percentage || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Phone & Identity -->
            <Card>
                <CardHeader>
                    <CardTitle>Phone & Identity Formatting</CardTitle>
                    <CardDescription>Format phone numbers, SSN, and EIN with proper separators</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">US Phone Number</div>
                            <Input v-model="phone" :formatter="phoneFormatter()" placeholder="(555) 123-4567" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ phone || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Social Security Number</div>
                            <Input v-model="ssn" :formatter="ssnFormatter()" placeholder="XXX-XX-XXXX" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ ssn || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">EIN (Employer ID)</div>
                            <Input v-model="ein" :formatter="einFormatter()" placeholder="XX-XXXXXXX" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ ein || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Credit Card & Payment -->
            <Card>
                <CardHeader>
                    <CardTitle>Credit Card & Payment</CardTitle>
                    <CardDescription>Format credit card numbers and ZIP codes</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Credit Card Number</div>
                            <Input v-model="creditCard" :formatter="creditCardFormatter()" placeholder="4111 1111 1111 1111" fluid>
                                <template #icon><CreditCard class="size-4" /></template>
                            </Input>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ creditCard || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">ZIP Code (5-digit)</div>
                            <Input v-model="zip5" :formatter="zipCodeFormatter()" placeholder="12345" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ zip5 || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">ZIP+4 Code</div>
                            <Input v-model="zip9" :formatter="zipCodeFormatter(true)" placeholder="12345-6789" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ zip9 || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Date & Time -->
            <Card>
                <CardHeader>
                    <CardTitle>Date & Time Formatting</CardTitle>
                    <CardDescription>Format dates and times with proper separators</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Date (MM/DD/YYYY)</div>
                            <Input v-model="dateValue" :formatter="dateFormatter()" placeholder="MM/DD/YYYY" fluid>
                                <template #icon><Calendar class="size-4" /></template>
                            </Input>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ dateValue || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Time (12-hour)</div>
                            <Input v-model="time12" :formatter="timeFormatter()" placeholder="HH:MM" fluid>
                                <template #icon><Clock class="size-4" /></template>
                            </Input>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ time12 || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Time (24-hour)</div>
                            <Input v-model="time24" :formatter="timeFormatter(true)" placeholder="HH:MM" fluid>
                                <template #icon><Clock class="size-4" /></template>
                            </Input>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ time24 || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Text Transformations -->
            <Card>
                <CardHeader>
                    <CardTitle>Text Transformations</CardTitle>
                    <CardDescription>Transform text case while typing</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Uppercase</div>
                            <Input v-model="uppercase" :formatter="uppercaseFormatter()" placeholder="TYPE HERE..." fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ uppercase || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Lowercase</div>
                            <Input v-model="lowercase" :formatter="lowercaseFormatter()" placeholder="type here..." fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ lowercase || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Pattern -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Pattern Formatting</CardTitle>
                    <CardDescription>Use # for digits and A for letters to create custom masks</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">License Plate (AAA-####)</div>
                            <Input v-model="licensePlate" :formatter="patternFormatter('AAA-####')" placeholder="ABC-1234" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ licensePlate || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Product Code (##-###-####)</div>
                            <Input v-model="productCode" :formatter="patternFormatter('##-###-####')" placeholder="12-345-6789" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ productCode || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Serial Number (AA##-####)</div>
                            <Input v-model="serialNumber" :formatter="patternFormatter('AA##-####')" placeholder="AB12-3456" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ serialNumber || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Formatter -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Formatter</CardTitle>
                    <CardDescription>Create your own formatters with format and parse functions</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Hex Color (adds #)</div>
                            <Input v-model="hexColor" :formatter="hexColorFormatter" placeholder="#FFFFFF" fluid />
                            <div class="mt-2 text-xs text-muted-foreground flex items-center gap-2">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ hexColor || '(empty)' }}</code>
                                <span v-if="hexColor" class="inline-block size-4 rounded border" :style="{ backgroundColor: '#' + hexColor }" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Slug (lowercase, hyphens)</div>
                            <Input v-model="slug" :formatter="slugFormatter" placeholder="my-page-slug" fluid />
                            <div class="mt-2 text-xs text-muted-foreground">
                                Raw value: <code class="bg-muted px-1 py-0.5 rounded">{{ slug || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-muted/50 rounded-lg">
                        <div class="text-xs font-medium mb-2">Custom Formatter Example:</div>
                        <pre class="text-xs overflow-x-auto"><code>import { createFormatter, type InputFormatter } from '@/utils';

// Using createFormatter helper
const hexColorFormatter = createFormatter(
    (value) => value ? `#${String(value).toUpperCase()}` : '',
    (display) => display.replace(/^#/, '').toUpperCase().slice(0, 6)
);

// Or implement InputFormatter interface directly
const slugFormatter: InputFormatter = {
    format: (value) => String(value || '').toLowerCase().replace(/\s+/g, '-').replace(/--+/g, '-'),
    parse: (display) => display.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-'),
};</code></pre>
                    </div>
                </CardContent>
            </Card>

            <!-- With Form Fields -->
            <Card>
                <CardHeader>
                    <CardTitle>With Form Labels</CardTitle>
                    <CardDescription>Formatted inputs integrated with LabelField component</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <LabelField label="Annual Salary" name="salary">
                            <div class="flex items-center gap-2">
                                <span class="text-muted-foreground">$</span>
                                <Input v-model="salary" :formatter="currencyFormatter()" placeholder="0.00" fluid />
                            </div>
                        </LabelField>
                        <LabelField label="Phone Number" name="contactPhone" required>
                            <Input v-model="contactPhone" :formatter="phoneFormatter()" placeholder="(555) 123-4567" fluid />
                        </LabelField>
                        <LabelField label="Credit Card" name="cardNumber" hint="We accept Visa, Mastercard, and Amex">
                            <Input v-model="cardNumber" :formatter="creditCardFormatter()" placeholder="4111 1111 1111 1111" fluid>
                                <template #icon><CreditCard class="size-4" /></template>
                            </Input>
                        </LabelField>
                        <LabelField label="Discount Rate" name="discount" required :error="discountError">
                            <Input v-model="discount" :formatter="percentageFormatter(1)" placeholder="0.0%" fluid invalid />
                        </LabelField>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { LabelField } from '@/components/ui/label';
import { CreditCard, Calendar, Clock } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';
import {
    numberFormatter,
    currencyFormatter,
    percentageFormatter,
    phoneFormatter,
    ssnFormatter,
    einFormatter,
    creditCardFormatter,
    zipCodeFormatter,
    dateFormatter,
    timeFormatter,
    uppercaseFormatter,
    lowercaseFormatter,
    patternFormatter,
    createFormatter,
    type InputFormatter,
} from '@/utils';

const { sidebarItems } = useShowcaseNav();

const pageTabs = [
    { href: '/admin/components/forms/input', title: 'Input' },
    { href: '/admin/components/forms/input/masks', title: 'Input Masks' },
];

// Number formatting
const numberInteger = ref('');
const numberDecimals = ref('');
const numberPositive = ref('');

// Currency
const currencyPrice = ref('');
const currencyNegative = ref('');
const percentage = ref('');

// Phone & Identity
const phone = ref('');
const ssn = ref('');
const ein = ref('');

// Credit Card & Payment
const creditCard = ref('');
const zip5 = ref('');
const zip9 = ref('');

// Date & Time
const dateValue = ref('');
const time12 = ref('');
const time24 = ref('');

// Text transformations
const uppercase = ref('');
const lowercase = ref('');

// Custom patterns
const licensePlate = ref('');
const productCode = ref('');
const serialNumber = ref('');

// Custom formatters
const hexColor = ref('');
const slug = ref('');

// Custom formatter implementations
const hexColorFormatter = createFormatter(
    (value) => value ? `#${String(value).toUpperCase()}` : '',
    (display) => display.replace(/^#/, '').replace(/[^0-9A-Fa-f]/g, '').toUpperCase().slice(0, 6)
);

const slugFormatter: InputFormatter = {
    format: (value) => String(value || '').toLowerCase().replace(/\s+/g, '-').replace(/--+/g, '-'),
    parse: (display) => display.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-'),
};

// Form fields
const salary = ref('');
const contactPhone = ref('');
const cardNumber = ref('');
const discount = ref('');
const discountError = 'Discount must be between 0% and 100%';
</script>
