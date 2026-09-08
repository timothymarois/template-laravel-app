<template>
    <AppLayout title="API keys" pageTitle="API keys">
        <template #headerAction>
            <Button label="Create key" class="cursor-pointer" @click="startCreate" />
        </template>

        <div class="space-y-4">
            <Alert v-if="flash.status" variant="success">
                <AlertDescription>{{ flash.status }}</AlertDescription>
            </Alert>

            <Card>
                <template #content>
                    <p class="text-sm text-muted-foreground mb-4">
                        Keys authenticate machine callers against the API. A key is shown once, at the
                        moment it is created, and only its hash is stored — if it is lost, revoke it and
                        create another.
                    </p>

                    <Table :items="keys" :columns="columns">
                        <template #abilities="{ data }">
                            <div class="flex flex-wrap gap-1">
                                <Badge v-for="ability in data.abilities" :key="ability" variant="secondary">
                                    {{ ability }}
                                </Badge>
                            </div>
                        </template>
                        <template #last_used_at="{ data }">
                            {{ data.last_used_at ? formatDatetime(data.last_used_at) : 'Never' }}
                        </template>
                        <template #expires_at="{ data }">
                            <span :class="isExpired(data) ? 'text-destructive' : ''">
                                {{ data.expires_at ? formatDatetime(data.expires_at) : 'Never' }}
                            </span>
                        </template>
                        <template #actions="{ data }">
                            <Button
                                variant="ghost"
                                label="Revoke"
                                class="cursor-pointer"
                                @click="revoke(data)"
                            />
                        </template>
                    </Table>

                    <p v-if="!keys.length" class="text-sm text-muted-foreground py-6 text-center">
                        No API keys yet.
                    </p>
                </template>
            </Card>
        </div>

        <Dialog v-model:visible="creating" header="Create an API key">
            <form class="space-y-4" @submit.prevent="submit">
                <LabelField name="name" label="Name" required :error="form.errors.name">
                    <InputText v-model="form.name" fluid :invalid="!!form.errors.name" />
                </LabelField>
                <LabelField name="abilities" label="Abilities" required :error="form.errors.abilities">
                    <div class="space-y-2">
                        <label
                            v-for="ability in abilities"
                            :key="ability.value"
                            class="flex items-center gap-2 cursor-pointer"
                        >
                            <Checkbox v-model="form.abilities" :value="ability.value" />
                            <span class="text-sm">{{ ability.label }} <code class="text-xs text-muted-foreground">{{ ability.value }}</code></span>
                        </label>
                    </div>
                </LabelField>
                <LabelField name="lifetime_days" label="Expires after (days)" :error="form.errors.lifetime_days">
                    <InputText
                        v-model="form.lifetime_days"
                        type="number"
                        fluid
                        :disabled="neverExpires"
                        :invalid="!!form.errors.lifetime_days"
                    />
                    <label class="mt-2 flex items-center gap-2 cursor-pointer">
                        <Checkbox v-model="neverExpires" />
                        <span class="text-sm">Never expires</span>
                    </label>
                    <p v-if="neverExpires" class="mt-1 text-xs text-muted-foreground">
                        A key with no expiry stays valid until it is revoked. Prefer an expiry unless
                        the caller genuinely cannot rotate.
                    </p>
                </LabelField>
            </form>
            <template #footer>
                <FormErrors :errors="form.errors" />
                <Button
                    label="Create key"
                    class="cursor-pointer"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="submit"
                />
            </template>
        </Dialog>

        <Dialog v-model:visible="showIssued" header="Copy your API key now">
            <div class="space-y-3">
                <Alert variant="warning">
                    <AlertDescription>
                        This is the only time the key is shown. It is stored as a hash and cannot be
                        retrieved again.
                    </AlertDescription>
                </Alert>
                <div class="rounded border bg-muted/40 p-3 font-mono text-sm break-all">
                    {{ issuedKey?.plainText }}
                </div>
            </div>
            <template #footer>
                <Button label="Copy" class="cursor-pointer" @click="copyKey" />
                <Button variant="outline" label="Done" class="cursor-pointer" @click="dismissIssued" />
            </template>
        </Dialog>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import {
    Alert,
    AlertDescription,
    Badge,
    Button,
    Card,
    Checkbox,
    Dialog,
    FormErrors,
    DataTable as Table,
    Input as InputText,
    LabelField,
} from '@/components/ui';
import { formatDatetime } from '@/utils';

const props = defineProps({
    keys: { type: Array, required: true },
    abilities: { type: Array, required: true },
    issuedKey: { type: Object, default: null },
});

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const columns = [
    { key: 'name', header: 'Name' },
    { key: 'abilities', header: 'Abilities' },
    { key: 'last_used_at', header: 'Last used' },
    { key: 'expires_at', header: 'Expires' },
    { key: 'actions', header: '' },
];

const creating = ref(false);
const showIssued = ref(!!props.issuedKey);
const neverExpires = ref(false);

const form = useForm({
    name: '',
    abilities: [],
    lifetime_days: null,
});

const isExpired = (key) => !!key.expires_at && new Date(key.expires_at) < new Date();

const startCreate = () => {
    form.reset();
    form.clearErrors();
    neverExpires.value = false;
    creating.value = true;
};

const submit = () => {
    // 0 is the server's NEVER_EXPIRES sentinel; null lets the server apply its
    // default lifetime. The checkbox is the only way to reach 0.
    form.lifetime_days = neverExpires.value ? 0 : (form.lifetime_days || null);

    form.post(route('admin.api-keys.store'), {
        preserveScroll: true,
        onSuccess: () => {
            creating.value = false;
            showIssued.value = true;
        },
    });
};

const copyKey = async () => {
    await navigator.clipboard.writeText(props.issuedKey.plainText);
    toast.success('API key copied to clipboard');
};

/**
 * Clears the plaintext out of Inertia's history entry before reloading, so the
 * back button cannot surface a key the operator has already dismissed.
 */
const dismissIssued = () => {
    showIssued.value = false;
    router.clearHistory();
    router.reload({ replace: true, only: ['keys', 'issuedKey'] });
};

const revoke = (key) => {
    router.delete(route('admin.api-keys.destroy', key.id), { preserveScroll: true });
};
</script>
