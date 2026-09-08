<template>
    <AppLayout title="API keys" :pageTitle="`API keys (${keys.length})`" containerClass="p-0" :noScroll="true">
        <template #headerAction>
            <div class="flex items-center space-x-2">
                <Button label="Create key" class="cursor-pointer" @click="startCreate" />
            </div>
        </template>

        <template #default>
            <div class="bg-card">
                <Table
                    :items="keys"
                    :itemTotal="keys.length"
                    :columns="columns"
                    :activeColumnList="activeColumns"
                    :defaultColumnList="activeColumns"
                    emptyLabel="No API keys yet"
                    scrollable
                >
                    <template #abilities="{ data }">
                        <div class="flex flex-wrap gap-1">
                            <Badge v-for="ability in data.abilities" :key="ability" variant="secondary">
                                {{ ability }}
                            </Badge>
                        </div>
                    </template>
                    <template #last_used_at="{ data }">
                        <span :class="data.last_used_at ? '' : 'text-muted-foreground'">
                            {{ data.last_used_at ? formatDatetime(data.last_used_at) : 'Never' }}
                        </span>
                    </template>
                    <template #expires_at="{ data }">
                        <span :class="isExpired(data) ? 'text-destructive' : ''">
                            {{ data.expires_at ? formatDatetime(data.expires_at) : 'Never' }}
                        </span>
                    </template>
                    <template #created_at="{ data }">
                        {{ formatDatetime(data.created_at) }}
                    </template>
                    <template #actions="{ data }">
                        <Button
                            variant="ghost"
                            size="sm"
                            label="Revoke"
                            class="cursor-pointer"
                            @click="confirmRevoke(data)"
                        />
                    </template>
                </Table>
            </div>

            <Dialog v-model:visible="creating" header="Create API key">
                <form class="space-y-4 w-full" @submit.prevent="submit">
                    <LabelField name="name" label="Name" required :error="form.errors.name">
                        <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                    </LabelField>
                    <LabelField name="abilities" label="Abilities" required :error="form.errors.abilities">
                        <div class="space-y-2">
                            <label
                                v-for="ability in abilities"
                                :key="ability.value"
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <Checkbox v-model="form.abilities" :value="ability.value" />
                                <span class="text-sm">{{ ability.label }}</span>
                            </label>
                        </div>
                    </LabelField>
                    <LabelField name="lifetime_days" label="Expires after (days)" :error="form.errors.lifetime_days">
                        <InputText
                            id="lifetime_days"
                            v-model="form.lifetime_days"
                            type="number"
                            fluid
                            :disabled="neverExpires"
                            :invalid="!!form.errors.lifetime_days"
                        />
                    </LabelField>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <Checkbox v-model="neverExpires" />
                        <span class="text-sm">Never expires</span>
                    </label>

                    <!-- The summary sits under the fields, not in the footer: the
                         footer is a horizontal row, so an error block there is
                         squeezed beside the action instead of reading full width. -->
                    <FormErrors :errors="form.errors" :expandDefault="true" />

                    <button type="submit" class="hidden" tabindex="-1" aria-hidden="true"></button>
                </form>
                <template #footer>
                    <Button variant="outline" label="Cancel" class="cursor-pointer" @click="creating = false" />
                    <Button
                        label="Create key"
                        class="cursor-pointer"
                        :disabled="form.processing"
                        :loading="form.processing"
                        @click="submit"
                    />
                </template>
            </Dialog>

            <Dialog v-model:visible="showIssued" header="Copy your key now">
                <div class="space-y-3">
                    <Alert variant="warning">
                        <AlertDescription>
                            This is the only time the key is shown. Only its hash is stored.
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

            <DialogConfirmation
                v-model="revoking"
                title="Revoke API key"
                :message="`Revoke “${revokeTarget?.name}”? Any caller using it stops working immediately, and this cannot be undone.`"
                confirmLabel="Revoke"
                destructive
                @confirm="revoke"
            />
        </template>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import {
    Alert,
    AlertDescription,
    Badge,
    Button,
    Checkbox,
    DataTable as Table,
    Dialog,
    DialogConfirmation,
    FormErrors,
    Input as InputText,
    LabelField,
} from '@/components/ui';
import { formatDatetime } from '@/utils';

const props = defineProps({
    keys: { type: Array, required: true },
    abilities: { type: Array, required: true },
    issuedKey: { type: Object, default: null },
});

const columns = [
    { key: 'name', header: 'Name', frozen: true, style: 'min-width: 200px', locked: true },
    { key: 'abilities', header: 'Abilities', style: 'min-width: 200px' },
    { key: 'last_used_at', header: 'Last used', style: 'min-width: 180px' },
    { key: 'expires_at', header: 'Expires', style: 'min-width: 180px' },
    { key: 'created_at', header: 'Created', style: 'min-width: 180px' },
    { key: 'actions', header: '', style: 'min-width: 100px' },
];

// DataTable renders the columns named here, in this order — an empty list
// renders no columns at all, not even headers.
const activeColumns = columns.map((column) => column.key);

const creating = ref(false);
const showIssued = ref(!!props.issuedKey);
const neverExpires = ref(false);
const revoking = ref(false);
const revokeTarget = ref(null);

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
    // 0 is the server's NEVER_EXPIRES sentinel; null takes the default lifetime.
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
 * Clears the plaintext from Inertia's history entry before reloading, so the
 * back button cannot surface a key the operator has already dismissed.
 */
const dismissIssued = () => {
    showIssued.value = false;
    router.clearHistory();
    router.reload({ replace: true, only: ['keys', 'issuedKey'] });
};

const confirmRevoke = (key) => {
    revokeTarget.value = key;
    revoking.value = true;
};

const revoke = () => {
    const name = revokeTarget.value.name;

    router.delete(route('admin.api-keys.destroy', revokeTarget.value.id), {
        preserveScroll: true,
        // Confirm the outcome, not the click: the row disappearing is the only
        // other signal, and on a long list it can happen off-screen.
        onSuccess: () => toast.success(`API key “${name}” revoked`),
        onError: () => toast.error(`Could not revoke “${name}”`),
    });
};
</script>
