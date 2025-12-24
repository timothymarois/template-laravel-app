<template>
    <LayoutApp
        title="Components - Select"
        pageTitle="Select"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Select -->
            <Card>
                <CardHeader>
                    <CardTitle>Select</CardTitle>
                    <CardDescription>Dropdown selection with single value</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Select v-model="basicEmpty" :options="basicOptions" placeholder="Select option..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Select v-model="basicFilled" :options="basicOptions" placeholder="Select option..." clearable fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Select v-model="basicInvalid" :options="basicOptions" placeholder="Select option..." invalid clearable fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Select v-model="basicDisabledEmpty" :options="basicOptions" placeholder="Disabled" disabled fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled Clearable</div>
                            <Select v-model="basicDisabledFilled" :options="basicOptions" placeholder="Select option..." disabled clearable fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Select Searchable -->
            <Card>
                <CardHeader>
                    <CardTitle>Select Searchable</CardTitle>
                    <CardDescription>Select with search/filter functionality (combobox)</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Select
                                v-model="searchEmpty"
                                :options="countries"
                                searchable
                                placeholder="Search countries..."
                                search-placeholder="Type to search..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Select
                                v-model="searchFilled"
                                :options="countries"
                                searchable
                                clearable
                                placeholder="Search countries..."
                                search-placeholder="Type to search..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Select
                                v-model="searchInvalid"
                                :options="countries"
                                searchable
                                placeholder="Search countries..."
                                invalid
                                clearable
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                            <Select
                                v-model="searchClearable"
                                :options="techOptions"
                                searchable
                                clearable
                                placeholder="Select technology..."
                                search-placeholder="Search..."
                                fluid
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Select
                                v-model="searchDisabled"
                                :options="countries"
                                searchable
                                disabled
                                clearable
                                placeholder="Search countries..."
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Multi Select -->
            <Card>
                <CardHeader>
                    <CardTitle>Multi Select</CardTitle>
                    <CardDescription>Select multiple values from a list</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Select
                                v-model="multiEmpty"
                                :options="permissionOptions"
                                multiple
                                :chips="false"
                                placeholder="Select permissions..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Select
                                v-model="multiFilled"
                                :options="permissionOptions"
                                multiple
                                :chips="false"
                                clearable
                                placeholder="Select permissions..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Select
                                v-model="multiInvalid"
                                :options="permissionOptions"
                                multiple
                                :chips="false"
                                placeholder="Select permissions..."
                                invalid
                                clearable
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Many Selected</div>
                            <Select
                                v-model="multiMany"
                                :options="permissionOptions"
                                multiple
                                :chips="false"
                                clearable
                                placeholder="Select permissions..."
                                fluid
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Select
                                v-model="multiDisabled"
                                :options="permissionOptions"
                                multiple
                                :chips="false"
                                disabled
                                clearable
                                placeholder="Select..."
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Multi Select Searchable -->
            <Card>
                <CardHeader>
                    <CardTitle>Multi Select Searchable</CardTitle>
                    <CardDescription>Multi-select with search/filter functionality</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Select
                                v-model="multiSearchEmpty"
                                :options="countries"
                                multiple
                                searchable
                                :chips="false"
                                placeholder="Search countries..."
                                search-placeholder="Type to search..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Select
                                v-model="multiSearchFilled"
                                :options="countries"
                                multiple
                                searchable
                                :chips="false"
                                clearable
                                placeholder="Search and select..."
                                search-placeholder="Search countries..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Select
                                v-model="multiSearchInvalid"
                                :options="countries"
                                multiple
                                searchable
                                :chips="false"
                                placeholder="Search countries..."
                                invalid
                                clearable
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Many Selected</div>
                            <Select
                                v-model="multiSearchMany"
                                :options="countries"
                                multiple
                                searchable
                                :chips="false"
                                clearable
                                placeholder="Search countries..."
                                fluid
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Select
                                v-model="multiSearchDisabled"
                                :options="countries"
                                multiple
                                searchable
                                :chips="false"
                                disabled
                                clearable
                                placeholder="Search..."
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Multi Select Tags -->
            <Card>
                <CardHeader>
                    <CardTitle>Multi Select Tags</CardTitle>
                    <CardDescription>Multi-select with inline chip/tag display</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Default</div>
                            <Select
                                v-model="chipsDefault"
                                :options="techOptions"
                                multiple
                                placeholder="Select..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Secondary</div>
                            <Select
                                v-model="chipsSecondary"
                                :options="techOptions"
                                multiple
                                clearable
                                chip-variant="secondary"
                                placeholder="Select..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Select
                                v-model="chipsInvalid"
                                :options="techOptions"
                                multiple
                                placeholder="Select..."
                                invalid
                                clearable
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Outline</div>
                            <Select
                                v-model="chipsOutline"
                                :options="techOptions"
                                multiple
                                clearable
                                chip-variant="outline"
                                placeholder="Select..."
                                fluid
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Select
                                v-model="chipsDisabled"
                                :options="techOptions"
                                multiple
                                disabled
                                placeholder="Select..."
                                fluid
                            />
                        </div>
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
import { Select } from '@/components/ui/select-popover';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Basic Select
const basicEmpty = ref('');
const basicFilled = ref('option2');
const basicInvalid = ref('option1');
const basicDisabledEmpty = ref('');
const basicDisabledFilled = ref('option1');

// Searchable Select
const searchEmpty = ref('');
const searchFilled = ref('us');
const searchInvalid = ref('ca');
const searchClearable = ref('vue');
const searchDisabled = ref('ca');

// Multi Select
const multiEmpty = ref<string[]>([]);
const multiFilled = ref<string[]>(['read', 'write']);
const multiInvalid = ref<string[]>(['read']);
const multiMany = ref<string[]>(['read', 'write', 'delete']);
const multiDisabled = ref<string[]>(['read']);

// Multi Searchable
const multiSearchEmpty = ref<string[]>([]);
const multiSearchFilled = ref<string[]>(['us', 'ca']);
const multiSearchInvalid = ref<string[]>(['us']);
const multiSearchMany = ref<string[]>(['us', 'ca', 'mx', 'br']);
const multiSearchDisabled = ref<string[]>(['us']);

// Chip Variants Examples
const chipsDefault = ref<string[]>(['vue', 'react']);
const chipsSecondary = ref<string[]>(['vue', 'typescript']);
const chipsInvalid = ref<string[]>(['vue', 'react']);
const chipsOutline = ref<string[]>(['react', 'node']);
const chipsDisabled = ref<string[]>(['vue', 'typescript']);

// Options
const basicOptions = [
    { label: 'Option 1', value: 'option1' },
    { label: 'Option 2', value: 'option2' },
    { label: 'Option 3', value: 'option3' },
];

const permissionOptions = [
    { label: 'Read', value: 'read' },
    { label: 'Write', value: 'write' },
    { label: 'Delete', value: 'delete' },
    { label: 'Admin', value: 'admin' },
];

const techOptions = [
    { label: 'Vue.js', value: 'vue' },
    { label: 'React', value: 'react' },
    { label: 'TypeScript', value: 'typescript' },
    { label: 'JavaScript', value: 'javascript' },
    { label: 'Tailwind CSS', value: 'tailwind' },
    { label: 'Node.js', value: 'node' },
];

const countries = [
    { label: 'Argentina', value: 'ar' },
    { label: 'Australia', value: 'au' },
    { label: 'Austria', value: 'at' },
    { label: 'Belgium', value: 'be' },
    { label: 'Brazil', value: 'br' },
    { label: 'Canada', value: 'ca' },
    { label: 'Chile', value: 'cl' },
    { label: 'China', value: 'cn' },
    { label: 'Colombia', value: 'co' },
    { label: 'Denmark', value: 'dk' },
    { label: 'Finland', value: 'fi' },
    { label: 'France', value: 'fr' },
    { label: 'Germany', value: 'de' },
    { label: 'Greece', value: 'gr' },
    { label: 'India', value: 'in' },
    { label: 'Ireland', value: 'ie' },
    { label: 'Italy', value: 'it' },
    { label: 'Japan', value: 'jp' },
    { label: 'Mexico', value: 'mx' },
    { label: 'Netherlands', value: 'nl' },
    { label: 'New Zealand', value: 'nz' },
    { label: 'Norway', value: 'no' },
    { label: 'Poland', value: 'pl' },
    { label: 'Portugal', value: 'pt' },
    { label: 'South Korea', value: 'kr' },
    { label: 'Spain', value: 'es' },
    { label: 'Sweden', value: 'se' },
    { label: 'Switzerland', value: 'ch' },
    { label: 'United Kingdom', value: 'gb' },
    { label: 'United States', value: 'us' },
];
</script>
