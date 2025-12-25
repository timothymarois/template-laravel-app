<template>
    <LayoutApp
        title="Components - Input Masks"
        pageTitle="Input"
        :pageTabs="pageTabs"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Mask Syntax Reference -->
            <Card>
                <CardHeader>
                    <CardTitle>Input Masks</CardTitle>
                    <CardDescription>
                        Use the <code class="text-xs bg-muted px-1 py-0.5 rounded">MaskInput</code> component for live formatting while typing.
                        Powered by <a href="https://beholdr.github.io/maska/v3/#/" target="_blank" class="text-primary hover:underline">maska</a>.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="p-3 bg-muted/50 rounded-lg">
                        <div class="text-xs font-medium mb-2">Mask Characters:</div>
                        <div class="grid grid-cols-4 gap-2 text-xs">
                            <div><code class="bg-background px-1 py-0.5 rounded">#</code> = digit (0-9)</div>
                            <div><code class="bg-background px-1 py-0.5 rounded">A</code> = letter (uppercase)</div>
                            <div><code class="bg-background px-1 py-0.5 rounded">a</code> = letter (lowercase)</div>
                            <div><code class="bg-background px-1 py-0.5 rounded">*</code> = alphanumeric</div>
                        </div>
                        <div class="text-xs text-muted-foreground mt-2">Any other character is a literal separator (auto-inserted)</div>
                    </div>
                </CardContent>
            </Card>

            <!-- Input States -->
            <Card>
                <CardHeader>
                    <CardTitle>Input States</CardTitle>
                    <CardDescription>MaskInput supports the same states as regular Input: disabled, invalid, sizes, clearable, and icons</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Default</div>
                            <MaskInput v-model="stateDefault" mask="(###) ###-####" placeholder="(555) 123-4567" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <MaskInput v-model="stateDisabled" mask="(###) ###-####" placeholder="(555) 123-4567" fluid disabled />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <MaskInput v-model="stateInvalid" mask="(###) ###-####" placeholder="(555) 123-4567" fluid invalid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                            <MaskInput v-model="stateClearable" mask="(###) ###-####" placeholder="(555) 123-4567" fluid clearable />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mt-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Size: Small</div>
                            <MaskInput v-model="sizeSmall" mask="(###) ###-####" placeholder="(555) 123-4567" fluid size="small" />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Size: Default</div>
                            <MaskInput v-model="sizeDefault" mask="(###) ###-####" placeholder="(555) 123-4567" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Size: Large</div>
                            <MaskInput v-model="sizeLarge" mask="(###) ###-####" placeholder="(555) 123-4567" fluid size="large" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mt-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Icon</div>
                            <MaskInput v-model="withIcon" mask="(###) ###-####" placeholder="(555) 123-4567" fluid>
                                <template #icon><Phone class="size-4" /></template>
                            </MaskInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Icon + Invalid</div>
                            <MaskInput v-model="iconInvalid" mask="(###) ###-####" placeholder="(555) 123-4567" fluid invalid>
                                <template #icon><Phone class="size-4" /></template>
                            </MaskInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Icon + Clearable</div>
                            <MaskInput v-model="iconClearable" mask="(###) ###-####" placeholder="(555) 123-4567" fluid clearable>
                                <template #icon><Phone class="size-4" /></template>
                            </MaskInput>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Phone & Identity -->
            <Card>
                <CardHeader>
                    <CardTitle>Phone & Identity</CardTitle>
                    <CardDescription>Common patterns for phone numbers and identity documents</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">US Phone</div>
                            <MaskInput v-model="phone" mask="(###) ###-####" placeholder="(555) 123-4567" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">(###) ###-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ phone || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">SSN</div>
                            <MaskInput v-model="ssn" mask="###-##-####" placeholder="123-45-6789" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">###-##-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ ssn || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">EIN</div>
                            <MaskInput v-model="ein" mask="##-#######" placeholder="12-3456789" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##-#######</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ ein || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Credit Card & Payment -->
            <Card>
                <CardHeader>
                    <CardTitle>Payment</CardTitle>
                    <CardDescription>Credit card, expiration, and CVV masks</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Credit Card</div>
                            <MaskInput v-model="creditCard" mask="#### #### #### ####" placeholder="4111 1111 1111 1111" fluid>
                                <template #icon><CreditCard class="size-4" /></template>
                            </MaskInput>
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">#### #### #### ####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ creditCard || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Expiration</div>
                            <MaskInput v-model="expiration" mask="##/##" placeholder="12/25" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##/##</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ expiration || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">CVV</div>
                            <MaskInput v-model="cvv" mask="###" placeholder="123" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">###</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ cvv || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">CVV (Amex)</div>
                            <MaskInput v-model="cvv4" mask="####" placeholder="1234" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ cvv4 || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- ZIP Codes -->
            <Card>
                <CardHeader>
                    <CardTitle>ZIP Codes</CardTitle>
                    <CardDescription>Standard 5-digit and extended ZIP+4 formats</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">ZIP (5-digit)</div>
                            <MaskInput v-model="zip5" mask="#####" placeholder="12345" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">#####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ zip5 || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">ZIP+4</div>
                            <MaskInput v-model="zip9" mask="#####-####" placeholder="12345-6789" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">#####-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ zip9 || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Date & Time -->
            <Card>
                <CardHeader>
                    <CardTitle>Date & Time</CardTitle>
                    <CardDescription>Date and time input masks</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Date (US)</div>
                            <MaskInput v-model="dateUS" mask="##/##/####" placeholder="12/25/2024" fluid>
                                <template #icon><Calendar class="size-4" /></template>
                            </MaskInput>
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##/##/####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ dateUS || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Date (ISO)</div>
                            <MaskInput v-model="dateISO" mask="####-##-##" placeholder="2024-12-25" fluid>
                                <template #icon><Calendar class="size-4" /></template>
                            </MaskInput>
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">####-##-##</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ dateISO || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Time</div>
                            <MaskInput v-model="time" mask="##:##" placeholder="14:30" fluid>
                                <template #icon><Clock class="size-4" /></template>
                            </MaskInput>
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##:##</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ time || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Time (seconds)</div>
                            <MaskInput v-model="timeSec" mask="##:##:##" placeholder="14:30:45" fluid>
                                <template #icon><Clock class="size-4" /></template>
                            </MaskInput>
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##:##:##</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ timeSec || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Letters & Mixed -->
            <Card>
                <CardHeader>
                    <CardTitle>Letters & Mixed</CardTitle>
                    <CardDescription>Patterns with letters (A = uppercase, a = lowercase, * = alphanumeric)</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">License Plate</div>
                            <MaskInput v-model="licensePlate" mask="AAA-####" placeholder="ABC-1234" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">AAA-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ licensePlate || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Product Code</div>
                            <MaskInput v-model="productCode" mask="AA-###-####" placeholder="AB-123-4567" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">AA-###-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ productCode || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Serial Number</div>
                            <MaskInput v-model="serial" mask="**-****-****" placeholder="A1-B2C3-D4E5" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">**-****-****</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ serial || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- mask-value Option -->
            <Card>
                <CardHeader>
                    <CardTitle>The <code class="text-sm bg-muted px-1 py-0.5 rounded">mask-value</code> Option</CardTitle>
                    <CardDescription>
                        By default, v-model stores raw characters only. Add <code class="text-xs bg-muted px-1 rounded">mask-value</code> to store the formatted value instead.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 border rounded-lg bg-background">
                            <div class="text-xs font-medium mb-2 flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-muted text-muted-foreground">default</span>
                                Raw value stored
                            </div>
                            <MaskInput v-model="phoneRaw" mask="(###) ###-####" placeholder="(555) 123-4567" fluid />
                            <div class="mt-3 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">(###) ###-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono text-foreground">{{ phoneRaw || '""' }}</code></div>
                            </div>
                        </div>
                        <div class="p-4 border rounded-lg bg-background">
                            <div class="text-xs font-medium mb-2 flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-primary/10 text-primary">mask-value</span>
                                Formatted value stored
                            </div>
                            <MaskInput v-model="phoneFormatted" mask="(###) ###-####" mask-value placeholder="(555) 123-4567" fluid />
                            <div class="mt-3 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">(###) ###-####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono text-foreground">{{ phoneFormatted || '""' }}</code></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mt-4">
                        <div class="p-4 border rounded-lg bg-background">
                            <div class="text-xs font-medium mb-2 flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-muted text-muted-foreground">default</span>
                                Date raw
                            </div>
                            <MaskInput v-model="dateRaw" mask="##/##/####" placeholder="12/25/2024" fluid />
                            <div class="mt-3 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##/##/####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono text-foreground">{{ dateRaw || '""' }}</code></div>
                            </div>
                        </div>
                        <div class="p-4 border rounded-lg bg-background">
                            <div class="text-xs font-medium mb-2 flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-primary/10 text-primary">mask-value</span>
                                Date formatted
                            </div>
                            <MaskInput v-model="dateFormatted" mask="##/##/####" mask-value placeholder="12/25/2024" fluid />
                            <div class="mt-3 space-y-1 text-xs">
                                <div class="text-muted-foreground">mask: <code class="bg-muted px-1 py-0.5 rounded">##/##/####</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono text-foreground">{{ dateFormatted || '""' }}</code></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-muted/50 rounded-lg">
                        <pre class="text-xs overflow-x-auto"><code>&lt;!-- Raw value in v-model (default) --&gt;
&lt;MaskInput v-model="phone" mask="(###) ###-####" /&gt;
&lt;!-- phone = "5551234567" --&gt;

&lt;!-- Formatted value in v-model --&gt;
&lt;MaskInput v-model="phone" mask="(###) ###-####" mask-value /&gt;
&lt;!-- phone = "(555) 123-4567" --&gt;</code></pre>
                    </div>
                </CardContent>
            </Card>

            <!-- Text Transformers -->
            <Card>
                <CardHeader>
                    <CardTitle>Text Transformers</CardTitle>
                    <CardDescription>Use the <code class="text-xs bg-muted px-1 py-0.5 rounded">formatter</code> prop for case transformations</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Uppercase</div>
                            <Input v-model="uppercase" :formatter="uppercaseFormatter()" placeholder="TYPE HERE..." fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">formatter: <code class="bg-muted px-1 py-0.5 rounded">uppercaseFormatter()</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ uppercase || '""' }}</code></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Lowercase</div>
                            <Input v-model="lowercase" :formatter="lowercaseFormatter()" placeholder="type here..." fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">formatter: <code class="bg-muted px-1 py-0.5 rounded">lowercaseFormatter()</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ lowercase || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Formatter -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Formatter</CardTitle>
                    <CardDescription>Use <code class="text-xs bg-muted px-1 py-0.5 rounded">createFormatter()</code> for advanced custom logic</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Hex Color (adds #)</div>
                            <Input v-model="hexColor" :formatter="hexColorFormatter" placeholder="#FFFFFF" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">formatter: <code class="bg-muted px-1 py-0.5 rounded">hexColorFormatter</code></div>
                                <div class="text-muted-foreground flex items-center gap-2">
                                    value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ hexColor || '""' }}</code>
                                    <span v-if="hexColor" class="inline-block size-4 rounded border" :style="{ backgroundColor: '#' + hexColor }" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Slug (lowercase, hyphens)</div>
                            <Input v-model="slug" :formatter="slugFormatter" placeholder="my-page-slug" fluid />
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="text-muted-foreground">formatter: <code class="bg-muted px-1 py-0.5 rounded">slugFormatter</code></div>
                                <div class="text-muted-foreground">value: <code class="bg-muted px-1 py-0.5 rounded font-mono">{{ slug || '""' }}</code></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-muted/50 rounded-lg">
                        <pre class="text-xs overflow-x-auto"><code>import { createFormatter } from '@/utils';

const hexColorFormatter = createFormatter(
    (value) => value ? `#${String(value).toUpperCase()}` : '',
    (display) => display.replace(/^#/, '').replace(/[^0-9A-Fa-f]/g, '').toUpperCase().slice(0, 6),
    7 // maxLength: # + 6 hex chars
);

const slugFormatter = createFormatter(
    // format: display the slug value as-is
    (value) => String(value || ''),
    // parse: convert input to slug (lowercase, spaces→hyphens, remove invalid chars)
    (display) => display.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-')
);</code></pre>
                    </div>
                </CardContent>
            </Card>

            <!-- With Form Fields -->
            <Card>
                <CardHeader>
                    <CardTitle>With Form Labels</CardTitle>
                    <CardDescription>Masked inputs work seamlessly with LabelField components</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <LabelField label="Phone Number" name="contactPhone" required>
                            <MaskInput v-model="contactPhone" mask="(###) ###-####" placeholder="(555) 123-4567" fluid />
                            <template #hint>
                                <span class="font-mono">{{ contactPhone || '""' }}</span>
                            </template>
                        </LabelField>
                        <LabelField label="Credit Card" name="cardNumber">
                            <MaskInput v-model="cardNumber" mask="#### #### #### ####" placeholder="4111 1111 1111 1111" fluid>
                                <template #icon><CreditCard class="size-4" /></template>
                            </MaskInput>
                            <template #hint>
                                <span class="font-mono">{{ cardNumber || '""' }}</span>
                            </template>
                        </LabelField>
                        <LabelField label="Social Security Number" name="ssnField">
                            <MaskInput v-model="ssnField" mask="###-##-####" placeholder="123-45-6789" fluid />
                            <template #hint>
                                <span class="font-mono">{{ ssnField || '""' }}</span>
                            </template>
                        </LabelField>
                        <LabelField label="Date of Birth" name="dob">
                            <MaskInput v-model="dob" mask="##/##/####" placeholder="01/15/1990" fluid>
                                <template #icon><Calendar class="size-4" /></template>
                            </MaskInput>
                            <template #hint>
                                <span class="font-mono">{{ dob || '""' }}</span>
                            </template>
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
import {
    Input,
    MaskInput,
    uppercaseFormatter,
    lowercaseFormatter,
    createFormatter,
} from '@/components/ui/input';
import { LabelField } from '@/components/ui/label';
import { CreditCard, Calendar, Clock, Phone } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const pageTabs = [
    { href: '/admin/components/forms/input', title: 'Input' },
    { href: '/admin/components/forms/input/masks', title: 'Mask Input' },
    { href: '/admin/components/forms/input/tags', title: 'Tags Input' },
];

// Input States
const stateDefault = ref('');
const stateDisabled = ref('5551234567');
const stateInvalid = ref('555');
const stateClearable = ref('5551234567');
const sizeSmall = ref('');
const sizeDefault = ref('');
const sizeLarge = ref('');
const withIcon = ref('');
const iconInvalid = ref('555');
const iconClearable = ref('5551234567');

// Phone & Identity
const phone = ref('');
const ssn = ref('');
const ein = ref('');

// Credit Card & Payment
const creditCard = ref('');
const expiration = ref('');
const cvv = ref('');
const cvv4 = ref('');

// ZIP Codes
const zip5 = ref('');
const zip9 = ref('');

// Date & Time
const dateUS = ref('');
const dateISO = ref('');
const time = ref('');
const timeSec = ref('');

// Letters & Mixed
const licensePlate = ref('');
const productCode = ref('');
const serial = ref('');

// mask-value comparison
const phoneRaw = ref('');
const phoneFormatted = ref('');
const dateRaw = ref('');
const dateFormatted = ref('');

// Text transformations
const uppercase = ref('');
const lowercase = ref('');

// Custom formatters
const hexColor = ref('');
const slug = ref('');

const hexColorFormatter = createFormatter(
    (value) => value ? `#${String(value).toUpperCase()}` : '',
    (display) => display.replace(/^#/, '').replace(/[^0-9A-Fa-f]/g, '').toUpperCase().slice(0, 6),
    7
);

const slugFormatter = createFormatter(
    // format: display the slug value as-is
    (value) => String(value || ''),
    // parse: convert input to slug (lowercase, spaces→hyphens, remove invalid chars)
    (display) => display.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-')
);

// Form fields
const contactPhone = ref('');
const cardNumber = ref('');
const ssnField = ref('');
const dob = ref('');
</script>
