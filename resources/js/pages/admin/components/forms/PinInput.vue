<template>
    <AppLayout
        title="Components - PinInput"
        pageTitle="PinInput"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>6-digit OTP</CardTitle>
                    <CardDescription>Auto-advance focus, one-time-code autocomplete. Value: <code>{{ otp.join('') || '—' }}</code></CardDescription>
                </CardHeader>
                <CardContent>
                    <PinInput v-model="otp" :id="['1','2','3','4','5','6']">
                        <PinInputGroup>
                            <PinInputSlot v-for="(_, i) in 3" :key="i" :index="i" />
                        </PinInputGroup>
                        <PinInputSeparator />
                        <PinInputGroup>
                            <PinInputSlot v-for="(_, i) in 3" :key="i + 3" :index="i + 3" />
                        </PinInputGroup>
                    </PinInput>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>4-digit PIN (numeric only)</CardTitle>
                    <CardDescription>Type set to <code>number</code> rejects non-digits. Value: <code>{{ pin.join('') || '—' }}</code></CardDescription>
                </CardHeader>
                <CardContent>
                    <PinInput v-model="pin" type="number">
                        <PinInputGroup>
                            <PinInputSlot v-for="(_, i) in 4" :key="i" :index="i" />
                        </PinInputGroup>
                    </PinInput>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { PinInput, PinInputGroup, PinInputSeparator, PinInputSlot } from '@/components/ui/pin-input';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const otp = ref<string[]>([]);
const pin = ref<string[]>([]);
</script>
