<template>
    <LayoutApp
        title="Components - Checkbox & Radio"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Checkbox</CardTitle>
                    <CardDescription>Boolean selection controls</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic</h4>
                        <div class="flex items-center gap-6">
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
                        <h4 class="text-sm font-medium mb-3">Checkbox Group</h4>
                        <div class="space-y-2">
                            <label v-for="option in checkboxOptions" :key="option.value" class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="selectedCheckboxes" :value="option.value" />
                                <span class="text-sm">{{ option.label }}</span>
                            </label>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Radio Group</CardTitle>
                    <CardDescription>Single selection from multiple options</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Vertical Layout</h4>
                        <RadioGroup v-model="radioValue" class="space-y-2">
                            <div v-for="option in radioOptions" :key="option.value" class="flex items-center gap-2">
                                <RadioGroupItem :value="option.value" :id="`radio-${option.value}`" />
                                <Label :for="`radio-${option.value}`" class="cursor-pointer">{{ option.label }}</Label>
                            </div>
                        </RadioGroup>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Horizontal Layout</h4>
                        <RadioGroup v-model="radioValue2" class="flex gap-6">
                            <div v-for="option in radioOptions" :key="option.value" class="flex items-center gap-2">
                                <RadioGroupItem :value="option.value" :id="`radio2-${option.value}`" />
                                <Label :for="`radio2-${option.value}`" class="cursor-pointer">{{ option.label }}</Label>
                            </div>
                        </RadioGroup>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Disabled State</h4>
                        <RadioGroup v-model="radioValue3" class="space-y-2">
                            <div class="flex items-center gap-2">
                                <RadioGroupItem value="enabled" id="radio3-enabled" />
                                <Label for="radio3-enabled" class="cursor-pointer">Enabled option</Label>
                            </div>
                            <div class="flex items-center gap-2 opacity-50">
                                <RadioGroupItem value="disabled" id="radio3-disabled" disabled />
                                <Label for="radio3-disabled" class="cursor-not-allowed">Disabled option</Label>
                            </div>
                            <div class="flex items-center gap-2 opacity-50">
                                <RadioGroupItem value="disabled-checked" id="radio3-disabled-checked" disabled />
                                <Label for="radio3-disabled-checked" class="cursor-not-allowed">Disabled checked</Label>
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

const checkbox1 = ref(false);
const checkbox2 = ref(true);
const selectedCheckboxes = ref<string[]>([]);
const radioValue = ref('option1');
const radioValue2 = ref('option2');
const radioValue3 = ref('disabled-checked');

const checkboxOptions = [
    { label: 'Email notifications', value: 'email' },
    { label: 'SMS notifications', value: 'sms' },
    { label: 'Push notifications', value: 'push' },
];

const radioOptions = [
    { label: 'Option 1', value: 'option1' },
    { label: 'Option 2', value: 'option2' },
    { label: 'Option 3', value: 'option3' },
];
</script>
