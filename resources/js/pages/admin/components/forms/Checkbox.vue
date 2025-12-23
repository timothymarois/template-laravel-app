<template>
    <LayoutApp
        title="Components - Checkbox & Radio"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="grid grid-cols-2 gap-6">
            <!-- Checkbox Card -->
            <Card class="flex flex-col">
                <CardHeader>
                    <CardTitle>Checkbox</CardTitle>
                    <CardDescription>Boolean selection controls for multiple choices</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6 flex-1">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="checkbox1" />
                                <span class="text-sm">Unchecked</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="checkbox2" />
                                <span class="text-sm">Checked</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-not-allowed opacity-50">
                                <Checkbox :modelValue="false" disabled />
                                <span class="text-sm">Disabled</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-not-allowed opacity-50">
                                <Checkbox :modelValue="true" disabled />
                                <span class="text-sm">Disabled Checked</span>
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
                <CardContent class="space-y-6 flex-1">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-2 gap-3">
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
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();

// Checkbox states
const checkbox1 = ref(false);
const checkbox2 = ref(true);
const selectedNotifications = ref<string[]>(['email']);

// Radio states
const radioState1 = ref('');
const radioState2 = ref('selected');
const selectedPlan = ref('basic');

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
