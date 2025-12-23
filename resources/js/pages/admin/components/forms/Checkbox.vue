<template>
    <LayoutApp
        title="Components - Checkbox"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="grid grid-cols-2 gap-4">
            <!-- Checkbox Card -->
            <Card class="flex flex-col">
                <CardHeader>
                    <CardTitle>Checkbox</CardTitle>
                    <CardDescription>Boolean selection controls for multiple choices</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4 flex-1">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="checkbox1" />
                                <span class="text-sm">Unchecked</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="checkbox2" />
                                <span class="text-sm">Checked</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="checkboxInvalid" invalid />
                                <span class="text-sm">Invalid</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-not-allowed opacity-50">
                                <Checkbox :modelValue="false" disabled />
                                <span class="text-sm">Disabled</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-not-allowed opacity-50">
                                <Checkbox :modelValue="true" disabled />
                                <span class="text-sm">Disabled Checked</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="checkboxInvalidChecked" invalid />
                                <span class="text-sm">Invalid Checked</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Group Selection</h4>
                        <div class="space-y-2">
                            <label v-for="option in notificationOptions" :key="option.value" class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="selectedNotifications" :value="option.value" />
                                <span class="text-sm">{{ option.label }}</span>
                            </label>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Radio Card -->
            <Card class="flex flex-col">
                <CardHeader>
                    <CardTitle>Radio Group</CardTitle>
                    <CardDescription>Single selection from multiple options</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4 flex-1">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <RadioGroup v-model="radioState1">
                                    <RadioGroupItem value="unselected" id="radio-unselected" />
                                </RadioGroup>
                                <span class="text-sm">Unselected</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <RadioGroup v-model="radioState2">
                                    <RadioGroupItem value="selected" id="radio-selected" />
                                </RadioGroup>
                                <span class="text-sm">Selected</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <RadioGroup v-model="radioInvalid">
                                    <RadioGroupItem value="invalid" id="radio-invalid" invalid />
                                </RadioGroup>
                                <span class="text-sm">Invalid</span>
                            </label>
                            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                                <RadioGroup model-value="">
                                    <RadioGroupItem value="disabled" id="radio-disabled" disabled />
                                </RadioGroup>
                                <span class="text-sm">Disabled</span>
                            </div>
                            <div class="flex items-center gap-2 cursor-not-allowed opacity-50">
                                <RadioGroup model-value="disabled-checked">
                                    <RadioGroupItem value="disabled-checked" id="radio-disabled-checked" disabled />
                                </RadioGroup>
                                <span class="text-sm">Disabled Selected</span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <RadioGroup v-model="radioInvalidSelected">
                                    <RadioGroupItem value="invalid-selected" id="radio-invalid-selected" invalid />
                                </RadioGroup>
                                <span class="text-sm">Invalid Selected</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Group Selection</h4>
                        <RadioGroup v-model="selectedPlan" class="space-y-2">
                            <div v-for="option in planOptions" :key="option.value" class="flex items-center gap-2">
                                <RadioGroupItem :value="option.value" :id="`plan-${option.value}`" />
                                <Label :for="`plan-${option.value}`" class="cursor-pointer">{{ option.label }}</Label>
                            </div>
                        </RadioGroup>
                    </div>
                </CardContent>
            </Card>

            <!-- Toggle Switch Card -->
            <Card class="flex flex-col col-span-2">
                <CardHeader>
                    <CardTitle>Toggle Switch</CardTitle>
                    <CardDescription>Binary on/off controls for settings and preferences</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6 flex-1">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="flex items-center gap-8">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="switchOff" />
                                <span class="text-sm">Off</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="switchOn" />
                                <span class="text-sm">On</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-not-allowed">
                                <Switch :defaultValue="false" disabled />
                                <span class="text-sm text-muted-foreground">Disabled</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-not-allowed">
                                <Switch :defaultValue="true" disabled />
                                <span class="text-sm text-muted-foreground">Disabled On</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Dynamic Label</h4>
                        <div class="flex items-center gap-8">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="dynamicSwitch" />
                                <span class="text-sm">{{ dynamicSwitch ? 'Enabled' : 'Disabled' }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="activeSwitch" />
                                <span class="text-sm font-medium" :class="activeSwitch ? 'text-green-600' : 'text-muted-foreground'">
                                    {{ activeSwitch ? 'Active' : 'Inactive' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Color Variants</h4>
                        <div class="flex items-center gap-8">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="colorDefault" />
                                <span class="text-sm">Default</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="colorGreen" class="data-[state=checked]:bg-green-600" />
                                <span class="text-sm">Green</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="colorBlue" class="data-[state=checked]:bg-blue-600" />
                                <span class="text-sm">Blue</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="colorRed" class="data-[state=checked]:bg-red-600" />
                                <span class="text-sm">Red</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Labels</h4>
                        <div class="space-y-3 max-w-md">
                            <label class="flex items-center justify-between gap-4 cursor-pointer">
                                <div>
                                    <div class="text-sm font-medium">Dark mode</div>
                                    <div class="text-xs text-muted-foreground">Use dark theme across the application</div>
                                </div>
                                <Switch v-model="darkMode" />
                            </label>
                            <label class="flex items-center justify-between gap-4 cursor-pointer">
                                <div>
                                    <div class="text-sm font-medium">Email notifications</div>
                                    <div class="text-xs text-muted-foreground">Receive updates about your account</div>
                                </div>
                                <Switch v-model="emailNotifs" />
                            </label>
                            <label class="flex items-center justify-between gap-4 cursor-pointer">
                                <div>
                                    <div class="text-sm font-medium">Two-factor authentication</div>
                                    <div class="text-xs text-muted-foreground">Add an extra layer of security</div>
                                </div>
                                <Switch v-model="twoFactor" />
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Sizes</h4>
                        <div class="flex items-center gap-8">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="sizeSmall" size="sm" />
                                <span class="text-sm">Small</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <Switch v-model="sizeDefault" />
                                <span class="text-sm">Default</span>
                            </label>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/form';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();

// Checkbox states
const checkbox1 = ref(false);
const checkbox2 = ref(true);
const checkboxInvalid = ref(false);
const checkboxInvalidChecked = ref(true);
const selectedNotifications = ref<string[]>(['email']);

// Radio states
const radioState1 = ref('');
const radioState2 = ref('selected');
const radioInvalid = ref('');
const radioInvalidSelected = ref('invalid-selected');
const selectedPlan = ref('basic');

// Switch states
const switchOff = ref(false);
const switchOn = ref(true);
const dynamicSwitch = ref(false);
const activeSwitch = ref(true);
const colorDefault = ref(true);
const colorGreen = ref(true);
const colorBlue = ref(true);
const colorRed = ref(true);
const darkMode = ref(false);
const emailNotifs = ref(true);
const twoFactor = ref(false);
const sizeSmall = ref(true);
const sizeDefault = ref(true);

const notificationOptions = [
    { label: 'Email notifications', value: 'email' },
    { label: 'SMS notifications', value: 'sms' },
    { label: 'Push notifications', value: 'push' },
];

const planOptions = [
    { label: 'Basic Plan', value: 'basic' },
    { label: 'Pro Plan', value: 'pro' },
    { label: 'Enterprise Plan', value: 'enterprise' },
];
</script>
