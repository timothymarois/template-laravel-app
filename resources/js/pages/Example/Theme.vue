<template>
    <Head title="Theme" />
    <LayoutDefault>
        <PageHeader title="Theme" />
        <PageMain>
            <div>
                <div class="flex flex-col space-y-4">
                    <!-- <div>filter bar</div> -->
                    <div class="flex space-x-4">
                        <Card>
                            <template #header>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                    <div>Edit</div>
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-4 w-full">
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormSlot name="first_name" label="First Name" required tooltip="This is a tooltip with some cool information about First Name fields. You can type an entire paragraph here, it is really cool.">
                                            <InputText id="first_name" v-model="form.first_name" type="text" fluid />
                                        </AtlasFormSlot>
                                        <AtlasFormSlot name="last_name" label="Last Name" required >
                                            <InputText id="last_name" v-model="form.last_name" type="text" fluid />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="email" label="Email" required>
                                            <InputText id="email" v-model="form.email" type="text" fluid />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="roles" label="Roles">
                                            <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="roles" label="Roles (chips)">
                                            <MultiSelect v-model="form.roles" display="chip" :options="roles" option-label="name" option-value="id" fluid filter :maxSelectedLabels="6" />
                                        </AtlasFormSlot>
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
                                        <AtlasFormSlot name="first_name" label="First Name" required tooltip="This is a tooltip with some cool information about First Name fields. You can type an entire paragraph here, it is really cool.">
                                            <InputText id="first_name" v-model="form.first_name" type="text" fluid :disabled="true" />
                                        </AtlasFormSlot>
                                        <AtlasFormSlot name="last_name" label="Last Name" required >
                                            <InputText id="last_name" v-model="form.last_name" type="text" fluid :disabled="true"  />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="email" label="Email" required>
                                            <InputText id="email" v-model="form.email" type="text" fluid :disabled="true"  />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="roles" label="Roles">
                                            <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter :disabled="true"  />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="roles" label="Roles (chips)">
                                            <MultiSelect v-model="form.roles" display="chip" :options="roles" option-label="name" option-value="id" fluid filter :maxSelectedLabels="6" :disabled="true"  />
                                        </AtlasFormSlot>
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
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormSlot name="amount" label="Amount" required>
                                            <InputNumber id="amount" v-model="form.amount" mode="currency" currency="USD" locale="en-US" fluid placeholder="$0.00" />
                                        </AtlasFormSlot>
                                        <AtlasFormSlot name="roles" label="Roles">
                                            <Select v-model="form.type" showClear :options="types" option-label="name" option-value="id" fluid />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="when" label="Charge">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment1" name="payment" value="Now" label="Now" />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment2" name="payment" value="Later" label="Later" />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment3" name="payment" value="disabled" label="Disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="agree" label="Terms">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="I agree to the terms" />
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="Terms disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormSlot>
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
                                    <div class="w-full flex items-center space-x-4">
                                        <AtlasFormSlot name="amount" label="Amount" required>
                                            <InputNumber id="amount" v-model="form.amount" mode="currency" currency="USD" locale="en-US" fluid placeholder="$0.00" :disabled="true"  />
                                        </AtlasFormSlot>
                                        <AtlasFormSlot name="roles" label="Roles">
                                            <Select v-model="form.type" showClear :options="types" option-label="name" option-value="id" fluid :disabled="true"  />
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="when" label="Charge">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment1" name="payment" value="Now" label="Now" :disabled="true"  />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment2" name="payment" value="Later" label="Later" :disabled="true"  />
                                                <AtlasFormRadioButton v-model="form.payment" inputId="payment3" name="payment" value="disabled" label="Disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormSlot>
                                    </div>
                                    <div class="w-full">
                                        <AtlasFormSlot name="agree" label="Terms">
                                            <div class="space-y-2 mt-2">
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="I agree to the terms" :disabled="true"  />
                                                <AtlasFormCheckbox v-model="form.agree" inputId="agree" binary label="Terms disabled" :disabled="true" />
                                            </div>
                                        </AtlasFormSlot>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <Button label="Save" @click="open('TEST')" :disabled="true"  />
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </PageMain>
    </LayoutDefault>
</template>

<script setup>
import { useModal } from '@atlas/composables';

const { open } = useModal();

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
</script>
