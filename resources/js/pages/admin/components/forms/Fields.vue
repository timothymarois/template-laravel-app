<template>
    <LayoutApp
        title="Components - Form Fields"
        pageTitle="Fields"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Label Field Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Label Field</CardTitle>
                    <CardDescription>Form field wrapper with label, error, and tooltip support. Labels are clickable to focus inputs.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <LabelField label="Basic Label" name="basic">
                            <Input id="basic" placeholder="Click label to focus" fluid />
                        </LabelField>

                        <LabelField label="Required Field" name="required" required>
                            <Input id="required" placeholder="Required input" fluid />
                        </LabelField>

                        <LabelField label="With Error" name="error" error="This field is required">
                            <Input id="error" placeholder="Has error" invalid fluid />
                        </LabelField>

                        <LabelField label="With Tooltip" name="tooltip" tooltip="This is helpful information about the field">
                            <Input id="tooltip" placeholder="Hover the icon" fluid />
                        </LabelField>
                    </div>
                </CardContent>
            </Card>

            <!-- Slider Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Slider</CardTitle>
                    <CardDescription>Range slider input for selecting numeric values</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Default</span>
                                    <span class="text-sm font-medium">{{ sliderDefault[0] }}</span>
                                </div>
                                <Slider v-model="sliderDefault" :max="100" :step="1" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">With Steps (10)</span>
                                    <span class="text-sm font-medium">{{ sliderSteps[0] }}</span>
                                </div>
                                <Slider v-model="sliderSteps" :max="100" :step="10" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Min/Max (20-80)</span>
                                    <span class="text-sm font-medium">{{ sliderMinMax[0] }}</span>
                                </div>
                                <Slider v-model="sliderMinMax" :min="20" :max="80" :step="1" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Disabled</span>
                                    <span class="text-sm font-medium">{{ sliderDisabled[0] }}</span>
                                </div>
                                <Slider v-model="sliderDisabled" :max="100" :step="1" disabled />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Range Selection</span>
                                    <span class="text-sm font-medium">{{ sliderRange[0] }} - {{ sliderRange[1] }}</span>
                                </div>
                                <Slider v-model="sliderRange" :max="100" :step="1" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Volume</span>
                                    <span class="text-sm font-medium">{{ sliderVolume[0] }}%</span>
                                </div>
                                <Slider v-model="sliderVolume" :max="100" :step="1" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Price Range</span>
                                    <span class="text-sm font-medium">${{ sliderPrice[0] }} - ${{ sliderPrice[1] }}</span>
                                </div>
                                <Slider v-model="sliderPrice" :max="1000" :step="10" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Rating (0.5 steps)</span>
                                    <span class="text-sm font-medium">{{ sliderRating[0] }} stars</span>
                                </div>
                                <Slider v-model="sliderRating" :min="1" :max="5" :step="0.5" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Realistic Form Examples -->
            <div class="grid grid-cols-2 gap-4">
                <!-- User Registration Form -->
                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Create Account</CardTitle>
                        <CardDescription>Register for a new account to get started</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 flex-1">
                        <div class="grid grid-cols-2 gap-4">
                            <LabelField label="First Name" name="reg-firstName" required>
                                <Input id="reg-firstName" v-model="registerForm.firstName" placeholder="John" fluid />
                            </LabelField>
                            <LabelField label="Last Name" name="reg-lastName" required>
                                <Input id="reg-lastName" v-model="registerForm.lastName" placeholder="Doe" fluid />
                            </LabelField>
                        </div>

                        <LabelField label="Email Address" name="reg-email" required>
                            <Input id="reg-email" v-model="registerForm.email" type="email" placeholder="john@example.com" fluid />
                        </LabelField>

                        <div class="grid grid-cols-2 gap-4">
                            <LabelField label="Password" name="reg-password" required>
                                <Input id="reg-password" v-model="registerForm.password" type="password" placeholder="Create password" fluid />
                            </LabelField>
                            <LabelField label="Confirm Password" name="reg-confirmPassword" required>
                                <Input id="reg-confirmPassword" v-model="registerForm.confirmPassword" type="password" placeholder="Confirm password" fluid />
                            </LabelField>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-start gap-2 cursor-pointer">
                                <Checkbox v-model="registerForm.terms" class="mt-0.5" />
                                <span class="text-sm text-muted-foreground">I agree to the <a href="#" class="text-primary underline">Terms of Service</a> and <a href="#" class="text-primary underline">Privacy Policy</a></span>
                            </label>
                        </div>
                    </CardContent>
                    <CardFooter class="border-t pt-4 mt-auto">
                        <Button fluid>Create Account</Button>
                    </CardFooter>
                </Card>

                <!-- Payment Form -->
                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Payment Details</CardTitle>
                        <CardDescription>Enter your card information to complete purchase</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 flex-1">
                        <LabelField label="Name on Card" name="pay-name" required>
                            <Input id="pay-name" v-model="paymentForm.cardName" placeholder="John Doe" fluid />
                        </LabelField>

                        <LabelField label="Card Number" name="pay-cardNumber" required>
                            <Input id="pay-cardNumber" v-model="paymentForm.cardNumber" placeholder="4242 4242 4242 4242" fluid />
                        </LabelField>

                        <div class="grid grid-cols-2 gap-4">
                            <LabelField label="Expiry Date" name="pay-expiry" required>
                                <Input id="pay-expiry" v-model="paymentForm.expiry" placeholder="MM / YY" fluid />
                            </LabelField>
                            <LabelField label="CVC" name="pay-cvc" required tooltip="3-digit code on back of card">
                                <Input id="pay-cvc" v-model="paymentForm.cvc" placeholder="123" fluid />
                            </LabelField>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="paymentForm.saveCard" />
                                <span class="text-sm">Save card for future purchases</span>
                            </label>
                        </div>
                    </CardContent>
                    <CardFooter class="border-t pt-4 mt-auto flex justify-between items-center">
                        <span class="text-lg font-semibold">Total: $99.00</span>
                        <Button>Process Payment</Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Input, Checkbox, LabelField } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import { Slider } from '@/components/ui/slider';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Slider examples
const sliderDefault = ref([50]);
const sliderSteps = ref([30]);
const sliderMinMax = ref([50]);
const sliderDisabled = ref([40]);
const sliderRange = ref([25, 75]);
const sliderVolume = ref([70]);
const sliderPrice = ref([100, 500]);
const sliderRating = ref([3.5]);

const registerForm = reactive({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    confirmPassword: '',
    terms: false,
});

const paymentForm = reactive({
    cardName: '',
    cardNumber: '',
    expiry: '',
    cvc: '',
    saveCard: true,
});
</script>
