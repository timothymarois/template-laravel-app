<template>
    <LayoutApp
        title="Components - Switch"
        pageTitle="Switch"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Toggle Switch States -->
            <Card>
                <CardHeader>
                    <CardTitle>Toggle Switch</CardTitle>
                    <CardDescription>Binary on/off controls for settings and preferences</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
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
                </CardContent>
            </Card>

            <!-- Dynamic Label -->
            <Card>
                <CardHeader>
                    <CardTitle>Dynamic Label</CardTitle>
                    <CardDescription>Labels that change based on switch state</CardDescription>
                </CardHeader>
                <CardContent>
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
                </CardContent>
            </Card>

            <!-- Color Variants -->
            <Card>
                <CardHeader>
                    <CardTitle>Color Variants</CardTitle>
                    <CardDescription>Custom color styling for different contexts</CardDescription>
                </CardHeader>
                <CardContent>
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
                </CardContent>
            </Card>

            <!-- With Labels -->
            <Card>
                <CardHeader>
                    <CardTitle>With Labels</CardTitle>
                    <CardDescription>Settings-style layout with description</CardDescription>
                </CardHeader>
                <CardContent>
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
                </CardContent>
            </Card>

            <!-- Sizes -->
            <Card>
                <CardHeader>
                    <CardTitle>Sizes</CardTitle>
                    <CardDescription>Different switch sizes for various contexts</CardDescription>
                </CardHeader>
                <CardContent>
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
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

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
</script>
