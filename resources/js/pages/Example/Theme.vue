<template>
    <LayoutApp
        title="Theme"
        pageTitle="Theme"
        :pageTabs="pageTabs"
        :pageNavItems="pageNavItems"
    >
        <div>
            <div class="flex flex-col space-y-4">
                <div class="flex space-x-4">
                    <Card>
                        <template #header>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center justify-between">
                                <div>Edit</div>
                                <ToggleSwitch v-model="form.checked" true-value="on" false-value="off" />
                                <!-- <div class="flex items-center space-x-2">
                                    <ManageMenu :items="manageItems" />
                                </div> -->
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-4 w-full">
                                <div class="w-full flex items-center space-x-4">
                                    <LabelField name="first_name" label="First Name" required tooltip="This is a tooltip with some cool information about First Name fields. You can type an entire paragraph here, it is really cool.">
                                        <InputText id="first_name" v-model="form.first_name" type="text" fluid />
                                    </LabelField>
                                    <LabelField name="last_name" label="Last Name" required >
                                        <InputText id="last_name" placeholder="Last Name" v-model="form.last_name" type="text" fluid />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="email" label="Email" required>
                                        <InputText id="email" v-model="form.email" type="text" fluid />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="roles" label="Roles">
                                        <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="roles" label="Roles (chips)">
                                        <MultiSelect v-model="form.roles" display="chip" :options="roles" option-label="name" option-value="id" fluid filter :maxSelectedLabels="6" />
                                    </LabelField>
                                </div>
                            </div>
                        </template>
                    </Card>
                    <Card>
                        <template #header>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center justify-between space-x-2">
                                <div>Edit (disabled)</div>
                                <ToggleSwitch v-model="form.checked" :disabled="true" true-value="on" false-value="off" />
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-4 w-full">
                                <div class="w-full flex items-center space-x-4">
                                    <LabelField name="first_name" label="First Name" required tooltip="This is a tooltip with some cool information about First Name fields. You can type an entire paragraph here, it is really cool.">
                                        <InputText id="first_name" v-model="form.first_name" type="text" fluid :disabled="true" />
                                    </LabelField>
                                    <LabelField name="last_name" label="Last Name" required >
                                        <InputText id="last_name" placeholder="Last Name" v-model="form.last_name" type="text" fluid :disabled="true"  />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="email" label="Email" required>
                                        <InputText id="email" v-model="form.email" type="text" fluid :disabled="true"  />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="roles" label="Roles">
                                        <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter :disabled="true"  />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="roles" label="Roles (chips)">
                                        <MultiSelect v-model="form.roles" display="chip" :options="roles" option-label="name" option-value="id" fluid filter :maxSelectedLabels="6" :disabled="true"  />
                                    </LabelField>
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
                                <Alert>
                                    <div>Your account is in good standing, but just keep in mind that we need to do some checks. Be sure to check out our help docs.</div>
                                </Alert>
                                <div class="w-full flex items-center space-x-4">
                                    <LabelField name="amount" label="Amount" required>
                                        <InputNumber id="amount" v-model="form.amount" mode="currency" currency="USD" locale="en-US" fluid placeholder="$0.00" />
                                    </LabelField>
                                    <LabelField name="roles" label="Roles">
                                        <Select v-model="form.type" showClear :options="types" option-label="name" option-value="id" fluid />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="when" label="Charge">
                                        <div class="space-y-2 mt-2">
                                            <LabelRadioButton v-model="form.payment" inputId="payment1" name="payment" value="Now" label="Now" />
                                            <LabelRadioButton v-model="form.payment" inputId="payment2" name="payment" value="Later" label="Later" />
                                            <LabelRadioButton v-model="form.payment" inputId="payment3" name="payment" value="disabled" label="Disabled" :disabled="true" />
                                        </div>
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="agree" label="Terms">
                                        <div class="space-y-2 mt-2">
                                            <LabelCheckbox v-model="form.agree" inputId="agree" binary label="I agree to the terms" />
                                            <LabelCheckbox v-model="form.agree" inputId="agree" binary label="Terms disabled" :disabled="true" />
                                        </div>
                                    </LabelField>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="flex items-center space-x-4">
                                <Button label="Save" @click="open('TEST')" />
                                <Button outlined label="Show blank" @click="open('BLANK_DRAWER')" />
                            </div>
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
                                <Alert warning>
                                    <div>Your account is <span class="font-semibold">negative</span>, but just keep in mind that we need to do some checks. Be sure to check out our help docs.</div>
                                    <template #actions>
                                        <Button size="small" label="Add funds" @click="open('TEST')" :disabled="true"  />
                                    </template>
                                </Alert>
                                <div class="w-full flex items-center space-x-4">
                                    <LabelField name="amount" label="Amount" required>
                                        <InputNumber id="amount" v-model="form.amount" mode="currency" currency="USD" locale="en-US" fluid placeholder="$0.00" :disabled="true"  />
                                    </LabelField>
                                    <LabelField name="roles" label="Roles">
                                        <Select v-model="form.type" showClear :options="types" option-label="name" option-value="id" fluid :disabled="true"  />
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="when" label="Charge">
                                        <div class="space-y-2 mt-2">
                                            <LabelRadioButton v-model="form.payment" inputId="payment1" name="payment" value="Now" label="Now" :disabled="true"  />
                                            <LabelRadioButton v-model="form.payment" inputId="payment2" name="payment" value="Later" label="Later" :disabled="true"  />
                                            <LabelRadioButton v-model="form.payment" inputId="payment3" name="payment" value="disabled" label="Disabled" :disabled="true" />
                                        </div>
                                    </LabelField>
                                </div>
                                <div class="w-full">
                                    <LabelField name="agree" label="Terms">
                                        <div class="space-y-2 mt-2">
                                            <LabelCheckbox v-model="form.agree" inputId="agree" binary label="I agree to the terms" :disabled="true"  />
                                            <LabelCheckbox v-model="form.agree" inputId="agree" binary label="Terms disabled" :disabled="true" />
                                        </div>
                                    </LabelField>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="flex items-center space-x-4">
                                <Button label="Save" @click="open('TEST')" :disabled="true"  />
                                <Button outlined label="Show blank" @click="open('BLANK_DRAWER')" :disabled="true" />
                            </div>
                        </template>
                    </Card>
                </div>
                <div class="flex space-x-4">
                    <Card pt:content:class="p-0">
                        <template #header>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                <div>Accordion</div>
                            </div>
                        </template>
                        <template #content>
                            <Accordion value="0">
                                <AccordionPanel value="0">
                                    <AccordionHeader>Header I</AccordionHeader>
                                    <AccordionContent>
                                        <p class="m-0">
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                        </p>
                                    </AccordionContent>
                                </AccordionPanel>
                                <AccordionPanel value="1">
                                    <AccordionHeader>Header II</AccordionHeader>
                                    <AccordionContent>
                                        <p class="m-0">
                                            Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim
                                            ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Consectetur, adipisci velit, sed quia non numquam eius modi.
                                        </p>
                                    </AccordionContent>
                                </AccordionPanel>
                                <AccordionPanel value="2" disabled>
                                    <AccordionHeader>Header III (disabled)</AccordionHeader>
                                    <AccordionContent>
                                        <p class="m-0">
                                            At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa
                                            qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus.
                                        </p>
                                    </AccordionContent>
                                </AccordionPanel>
                            </Accordion>
                        </template>
                    </Card>
                    <Card pt:content:class="p-0">
                        <template #header>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                                <div>Tabs</div>
                            </div>
                        </template>
                        <template #content>
                            <Tabs value="0">
                                <TabList>
                                    <Tab value="0">Header I</Tab>
                                    <Tab value="1">Header II</Tab>
                                    <Tab value="2">Header III</Tab>
                                </TabList>
                                <TabPanels>
                                    <TabPanel value="0">
                                        <p class="m-0">
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                        </p>
                                    </TabPanel>
                                    <TabPanel value="1">
                                        <p class="m-0">
                                            Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim
                                            ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Consectetur, adipisci velit, sed quia non numquam eius modi. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                        </p>
                                    </TabPanel>
                                    <TabPanel value="2">
                                        <p class="m-0">
                                            At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa
                                            qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                        </p>
                                    </TabPanel>
                                </TabPanels>
                            </Tabs>
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
        <template #footer>
            This is a page footer
        </template>
        <template #footerAction>
            [actions]
        </template>
    </LayoutApp>
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
    agree: false,
    checked: 'on'
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

const pageTabs = [
    { title: 'Overview', href: '/theme' },
    { title: 'Disabled', href: '/theme/table', disabled: true },
    { title: 'Settings', href: '/theme/settings' },
];

const pageNavItems = [
    { label: 'Overview', href: '/theme' },
    { label: 'Settings', href: '/theme/settings' },
    { label: 'Account', href: '/settings/account' },
    { label: 'Security', href: '/settings/security' },
    { label: 'Notifications', href: '/settings/notifications' },
];
</script>
