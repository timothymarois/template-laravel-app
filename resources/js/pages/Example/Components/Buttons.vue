<template>
    <Head title="Buttons - Components" />
    <PageHeader title="Buttons" />
    <PageMain>
        <div class="flex items-start justify-center space-x-4">
            <Card class="grow">
                <template #title>
                    Default
                </template>
                <template #content>
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-4">
                            <Button
                                label="Default"
                                @click="showSuccess"
                            />
                            <Button
                                label="Loading"
                                loading
                            />
                            <Button
                                label="Disabled"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                label="Default"
                                outlined
                                @click="showError"
                            />
                            <Button
                                label="Loading"
                                outlined
                                loading
                            />
                            <Button
                                label="Disabled"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                label="Default"
                                text
                            />
                            <Button
                                label="Loading"
                                text
                                loading
                            />
                            <Button
                                label="Disabled"
                                text
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                label="Default"
                                rounded
                            />
                            <Button
                                label="Loading"
                                rounded
                                loading
                            />
                            <Button
                                label="Disabled"
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                label="Default"
                                outlined
                                rounded
                            />
                            <Button
                                label="Loading"
                                outlined
                                rounded
                                loading
                            />
                            <Button
                                label="Disabled"
                                outlined
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                label="Default"
                                text
                                rounded
                            />
                            <Button
                                label="Loading"
                                text
                                rounded
                                loading
                            />
                            <Button
                                label="Disabled"
                                text
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                label="Default"
                            />
                            <Button
                                icon="pi pi-user"
                                label="Loading"
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                label="Disabled"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                label="Default"
                                outlined
                            />
                            <Button
                                icon="pi pi-user"
                                label="Loading"
                                outlined
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                label="Disabled"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                label="Default"
                                text
                            />
                            <Button
                                icon="pi pi-user"
                                label="Loading"
                                text
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                label="Disabled"
                                text
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button icon="pi pi-user" />
                            <Button
                                icon="pi pi-user"
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                outlined
                            />
                            <Button
                                icon="pi pi-user"
                                outlined
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                text
                            />
                            <Button
                                icon="pi pi-user"
                                text
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                text
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                rounded
                            />
                            <Button
                                icon="pi pi-user"
                                rounded
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                rounded
                                outlined
                            />
                            <Button
                                icon="pi pi-user"
                                rounded
                                outlined
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                rounded
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                icon="pi pi-user"
                                rounded
                                text
                            />
                            <Button
                                icon="pi pi-user"
                                rounded
                                text
                                loading
                            />
                            <Button
                                icon="pi pi-user"
                                rounded
                                text
                                disabled
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                label="Fluid"
                                fluid
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                label="Fluid"
                                fluid
                                outlined
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                label="Fluid"
                                fluid
                                text
                            />
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="grow">
                <template #title>
                    Small
                </template>
                <template #content>
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                label="Default"
                            />
                            <Button
                                size="small"
                                label="Loading"
                                loading
                            />
                            <Button
                                size="small"
                                label="Disabled"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                label="Default"
                                outlined
                            />
                            <Button
                                size="small"
                                label="Loading"
                                outlined
                                loading
                            />
                            <Button
                                size="small"
                                label="Disabled"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                label="Default"
                                text
                            />
                            <Button
                                size="small"
                                label="Loading"
                                text
                                loading
                            />
                            <Button
                                size="small"
                                label="Disabled"
                                text
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                label="Default"
                                rounded
                            />
                            <Button
                                size="small"
                                label="Loading"
                                rounded
                                loading
                            />
                            <Button
                                size="small"
                                label="Disabled"
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                label="Default"
                                outlined
                                rounded
                            />
                            <Button
                                size="small"
                                label="Loading"
                                outlined
                                rounded
                                loading
                            />
                            <Button
                                size="small"
                                label="Disabled"
                                outlined
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                label="Default"
                                text
                                rounded
                            />
                            <Button
                                size="small"
                                label="Loading"
                                text
                                rounded
                                loading
                            />
                            <Button
                                size="small"
                                label="Disabled"
                                text
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Default"
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Loading"
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Disabled"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Default"
                                outlined
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Loading"
                                outlined
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Disabled"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Default"
                                text
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Loading"
                                text
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                label="Disabled"
                                text
                                disabled
                            />
                        </div>

                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                outlined
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                outlined
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                text
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                text
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                text
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                outlined
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                outlined
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                text
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                text
                                loading
                            />
                            <Button
                                size="small"
                                icon="pi pi-user"
                                rounded
                                text
                                disabled
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                size="small"
                                label="Fluid"
                                fluid
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                size="small"
                                label="Fluid"
                                fluid
                                outlined
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                size="small"
                                label="Fluid"
                                fluid
                                text
                            />
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="grow">
                <template #title>
                    Large
                </template>
                <template #content>
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                label="Default"
                            />
                            <Button
                                size="large"
                                label="Loading"
                                loading
                            />
                            <Button
                                size="large"
                                label="Disabled"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                label="Default"
                                outlined
                            />
                            <Button
                                size="large"
                                label="Loading"
                                outlined
                                loading
                            />
                            <Button
                                size="large"
                                label="Disabled"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                label="Default"
                                text
                            />
                            <Button
                                size="large"
                                label="Loading"
                                text
                                loading
                            />
                            <Button
                                size="large"
                                label="Disabled"
                                text
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                label="Default"
                                rounded
                            />
                            <Button
                                size="large"
                                label="Loading"
                                rounded
                                loading
                            />
                            <Button
                                size="large"
                                label="Disabled"
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                label="Default"
                                outlined
                                rounded
                            />
                            <Button
                                size="large"
                                label="Loading"
                                outlined
                                rounded
                                loading
                            />
                            <Button
                                size="large"
                                label="Disabled"
                                outlined
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                label="Default"
                                text
                                rounded
                            />
                            <Button
                                size="large"
                                label="Loading"
                                text
                                rounded
                                loading
                            />
                            <Button
                                size="large"
                                label="Disabled"
                                text
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Default"
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Loading"
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Disabled"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Default"
                                outlined
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Loading"
                                outlined
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Disabled"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Default"
                                text
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Loading"
                                text
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                label="Disabled"
                                text
                                disabled
                            />
                        </div>

                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                outlined
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                outlined
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                text
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                text
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                text
                                disabled
                            />
                        </div>


                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                outlined
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                outlined
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                outlined
                                disabled
                            />
                        </div>
                        <div class="flex space-x-4">
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                text
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                text
                                loading
                            />
                            <Button
                                size="large"
                                icon="pi pi-user"
                                rounded
                                text
                                disabled
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                size="large"
                                label="Fluid"
                                fluid
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                size="large"
                                label="Fluid"
                                fluid
                                outlined
                            />
                        </div>
                        <div class="w-full">
                            <Button
                                size="large"
                                label="Fluid"
                                fluid
                                text
                            />
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </PageMain>
</template>
<script setup>
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const showSuccess = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'The project has been saved.', life: 3000 });
};

const showError = () => {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Could not process request. Please try again.', life: 4000 });
};
</script>
