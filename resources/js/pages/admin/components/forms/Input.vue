<template>
    <LayoutApp
        title="Components - Input"
        pageTitle="Input"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Input -->
            <Card>
                <CardHeader>
                    <CardTitle>Input</CardTitle>
                    <CardDescription>Text input fields with various configurations</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Input placeholder="Enter text..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Input v-model="inputValue" placeholder="Enter text..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                            <Input v-model="inputClearable" placeholder="Type to clear..." clearable fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Input v-model="inputDisabled" placeholder="Disabled" disabled fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Input Types -->
            <Card>
                <CardHeader>
                    <CardTitle>Input Types</CardTitle>
                    <CardDescription>Different input types for various data entry</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Text</div>
                            <Input type="text" placeholder="Enter text..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Email</div>
                            <Input type="email" placeholder="email@example.com" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Password</div>
                            <Input type="password" v-model="passwordValue" placeholder="Enter password..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Number</div>
                            <Input type="number" placeholder="0" fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Input Invalid -->
            <Card>
                <CardHeader>
                    <CardTitle>Input Invalid</CardTitle>
                    <CardDescription>Error states for form validation</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Input placeholder="Required field" invalid fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Input v-model="invalidValue" placeholder="Invalid input" invalid fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                            <Input v-model="invalidClearable" placeholder="Clear to fix..." invalid clearable fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Input v-model="invalidDisabled" placeholder="Invalid disabled" invalid disabled fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Input with Icon -->
            <Card>
                <CardHeader>
                    <CardTitle>Input with Icon</CardTitle>
                    <CardDescription>Input fields with icons inside for visual context</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Search</div>
                            <Input v-model="iconSearchValue" placeholder="Search..." fluid>
                                <template #icon><Search class="size-4" /></template>
                            </Input>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Email</div>
                            <Input v-model="iconEmailValue" type="email" placeholder="email@example.com" fluid>
                                <template #icon><Mail class="size-4" /></template>
                            </Input>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">User</div>
                            <Input v-model="iconUserValue" placeholder="Username" fluid>
                                <template #icon><User class="size-4" /></template>
                            </Input>
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Input placeholder="Locked" disabled fluid>
                                <template #icon><Lock class="size-4" /></template>
                            </Input>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Input Groups -->
            <Card>
                <CardHeader>
                    <CardTitle>Input Groups</CardTitle>
                    <CardDescription>Common patterns for grouping inputs with other elements</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Button</div>
                            <div class="flex gap-0 w-full">
                                <Input v-model="searchValue" placeholder="Search..." class="rounded-r-none" fluid />
                                <Button class="rounded-l-none shrink-0">Go</Button>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Select</div>
                            <div class="flex gap-0 w-full">
                                <Input v-model="amountValue" type="number" placeholder="0.00" class="rounded-r-none" fluid />
                                <Select v-model="currencyValue" :options="currencyOptions" class="w-20 shrink-0 rounded-l-none border-l-0" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Icon Button</div>
                            <div class="flex gap-0 w-full">
                                <Input placeholder="Search..." class="rounded-r-none" fluid />
                                <Button variant="outline" class="rounded-l-none border-l-0 shrink-0">
                                    <Search class="size-4" />
                                </Button>
                            </div>
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled Group</div>
                            <div class="flex gap-0 w-full">
                                <Input placeholder="Disabled..." disabled class="rounded-r-none" fluid />
                                <Button disabled class="rounded-l-none shrink-0">Go</Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Number Input -->
            <Card>
                <CardHeader>
                    <CardTitle>Number Input</CardTitle>
                    <CardDescription>Numeric input with increment/decrement buttons</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Default</div>
                            <NumberInput v-model="numberDefault" placeholder="0" />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Min/Max (0-10)</div>
                            <NumberInput v-model="numberMinMax" :min="0" :max="10" />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Step (0.5)</div>
                            <NumberInput v-model="numberStep" :step="0.5" :min="0" />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <NumberInput v-model="numberDisabled" disabled />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-4 mt-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <NumberInput v-model="numberInvalid" invalid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Fluid Width</div>
                            <NumberInput v-model="numberFluid" fluid :min="0" :max="100" />
                        </div>
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
import { Input, NumberInput } from '@/components/ui/input';
import { Select } from '@/components/ui/select-popover';
import { Button } from '@/components/ui/button';
import { Search, Mail, User, Lock } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Input states
const inputValue = ref('Hello world');
const inputClearable = ref('Clear me');
const inputDisabled = ref('Cannot edit');

// Password
const passwordValue = ref('secret123');

// Invalid states
const invalidValue = ref('Invalid data');
const invalidClearable = ref('Bad input');
const invalidDisabled = ref('Error');

// Input with icon
const iconSearchValue = ref('');
const iconEmailValue = ref('');
const iconUserValue = ref('johndoe');

// Input groups
const searchValue = ref('');
const amountValue = ref('');
const currencyValue = ref('USD');

const currencyOptions = [
    { label: 'USD', value: 'USD' },
    { label: 'EUR', value: 'EUR' },
    { label: 'GBP', value: 'GBP' },
];

// Number input
const numberDefault = ref(5);
const numberMinMax = ref(5);
const numberStep = ref(2.5);
const numberDisabled = ref(10);
const numberInvalid = ref(-1);
const numberFluid = ref(50);
</script>
