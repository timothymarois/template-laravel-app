<template>
    <Head title="Theme" />
    <LayoutDefault>
        <PageHeader title="Theme" />
        <PageMain>
            <div>
                <div class="flex flex-col space-y-4">
                    <!-- <div>filter bar</div> -->
                    <div class="flex space-x-4">
                        <Card pt:header:class="py-2">
                            <template #header>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center justify-between">
                                    <div>Edit</div>
                                    <div class="flex items-center space-x-2">
                                        <ManageMenu :items="manageItems" />
                                    </div>
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-4 w-full">
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormField name="first_name" label="First Name" required tooltip="This is a tooltip with some cool information about First Name fields. You can type an entire paragraph here, it is really cool.">
                                            <InputText id="first_name" v-model="form.first_name" type="text" fluid />
                                        </AtlasFormField>
                                        <AtlasFormField name="last_name" label="Last Name" required >
                                            <InputText id="last_name" v-model="form.last_name" type="text" fluid />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="email" label="Email" required>
                                            <InputText id="email" v-model="form.email" type="text" fluid />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="roles" label="Roles">
                                            <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="roles" label="Roles (chips)">
                                            <MultiSelect v-model="form.roles" display="chip" :options="roles" option-label="name" option-value="id" fluid filter :maxSelectedLabels="6" />
                                        </AtlasFormField>
                                    </div>
                                </div>
                            </template>
                        </Card>
                        <Card>
                            <template #header>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                    <div>Edit (disabled)</div>
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-4 w-full">
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormField name="first_name" label="First Name" required tooltip="This is a tooltip with some cool information about First Name fields. You can type an entire paragraph here, it is really cool.">
                                            <InputText id="first_name" v-model="form.first_name" type="text" fluid :disabled="true" />
                                        </AtlasFormField>
                                        <AtlasFormField name="last_name" label="Last Name" required >
                                            <InputText id="last_name" v-model="form.last_name" type="text" fluid :disabled="true"  />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="email" label="Email" required>
                                            <InputText id="email" v-model="form.email" type="text" fluid :disabled="true"  />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="roles" label="Roles">
                                            <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter :disabled="true"  />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="roles" label="Roles (chips)">
                                            <MultiSelect v-model="form.roles" display="chip" :options="roles" option-label="name" option-value="id" fluid filter :maxSelectedLabels="6" :disabled="true"  />
                                        </AtlasFormField>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                    <div class="flex space-x-4">
                        <Card>
                            <template #header>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                    <div>Cash</div>
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-4 w-full">
                                    <AtlasMessage>
                                        <div>Your account is in good standing, but just keep in mind that we need to do some checks. Be sure to check out our help docs.</div>
                                    </AtlasMessage>
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormField name="amount" label="Amount" required>
                                            <InputNumber id="amount" v-model="form.amount" mode="currency" currency="USD" locale="en-US" fluid placeholder="$0.00" />
                                        </AtlasFormField>
                                        <AtlasFormField name="roles" label="Roles">
                                            <Select v-model="form.type" showClear :options="types" option-label="name" option-value="id" fluid />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="when" label="Charge">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment1" name="payment" value="Now" label="Now" />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment2" name="payment" value="Later" label="Later" />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment3" name="payment" value="disabled" label="Disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="agree" label="Terms">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="I agree to the terms" />
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="Terms disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormField>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <Button label="Save" @click="open('TEST')" />
                            </template>
                        </Card>
                        <Card>
                            <template #header>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                    <div>Cash (disabled)</div>
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-4 w-full">
                                    <AtlasMessage warning>
                                        <div>Your account is <span class="font-semibold">negative</span>, but just keep in mind that we need to do some checks. Be sure to check out our help docs.</div>
                                        <template #actions>
                                            <Button size="small" label="Add funds" @click="open('TEST')" :disabled="true"  />
                                        </template>
                                    </AtlasMessage>
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormField name="amount" label="Amount" required>
                                            <InputNumber id="amount" v-model="form.amount" mode="currency" currency="USD" locale="en-US" fluid placeholder="$0.00" :disabled="true"  />
                                        </AtlasFormField>
                                        <AtlasFormField name="roles" label="Roles">
                                            <Select v-model="form.type" showClear :options="types" option-label="name" option-value="id" fluid :disabled="true"  />
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="when" label="Charge">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment1" name="payment" value="Now" label="Now" :disabled="true"  />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment2" name="payment" value="Later" label="Later" :disabled="true"  />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment3" name="payment" value="disabled" label="Disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormField>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormField name="agree" label="Terms">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="I agree to the terms" :disabled="true"  />
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="Terms disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormField>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <Button label="Save" @click="open('TEST')" :disabled="true"  />
                            </template>
                        </Card>
                    </div>
                    <div>
                        <Card pt:content:class="p-0">
                            <template #header>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                    <div>Virtual table</div>
                                </div>
                            </template>
                            <template #content>
                                <div>
                                    <DataTable :value="products" tableStyle="min-width: 50rem" paginator :rows="25" scrollable scrollHeight="300px">
                                        <Column field="code" header="Code" sortable></Column>
                                        <Column field="name" header="Name" sortable></Column>
                                        <Column field="category" header="Category"></Column>
                                        <Column field="quantity" header="Quantity"></Column>
                                    </DataTable>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </PageMain>
    </LayoutDefault>
</template>

<script setup>
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';  // optional
import Row from 'primevue/row';          // optional

import { useModal } from '@atlas/composables';

const { open } = useModal();

const products = ref(
    Array.from({ length: 100 }, (_, i) => ({
        code: `P-${String(i + 1).padStart(3, '0')}`,
        name: `Product ${i + 1}`,
        category: `Category ${((i % 10) + 1)}`,
        quantity: Math.floor(Math.random() * 100) + 1,
    }))
);

const form = useForm({
    first_name: 'John',
    last_name: null,
    amount: null,
    email: 'example@example.com',
    roles: [],
    type: 'credit',
    payment: 'disabled',
    agree: false
});

const roles = ref([
    { id: 'admin', name: 'Admin' },
    { id: 'user', name: 'User' },
    { id: 'guest', name: 'Guest' },
    { id: 'banned', name: 'Banned' },
    { id: 'pending', name: 'Pending' },
    { id: 'suspended', name: 'Suspended' },
    { id: 'deleted', name: 'Deleted' },
    { id: 'blacklisted', name: 'Blacklisted' },
    { id: 'archived', name: 'Archived' },
]);

const types = ref([
    { id: 'credit', name: 'Credit' },
    { id: 'debit', name: 'Debit' },
    { id: 'transfer', name: 'Transfer' },
    { id: 'deposit', name: 'Deposit' },
]);

const manageItems = [
    {
        label: 'Edit',
        icon: 'text-sm pi pi-pencil',
        action: 'edit'
    },
    {
        separator: true
    },
    {
        label: 'Archive',
        icon: 'text-sm pi pi-trash',
        action: 'delete'
    }
];
</script>
