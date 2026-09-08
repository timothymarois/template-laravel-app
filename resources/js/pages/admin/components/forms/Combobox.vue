<template>
    <AppLayout
        title="Components - Combobox"
        pageTitle="Combobox"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Combobox -->
            <Card>
                <CardHeader>
                    <CardTitle>Combobox</CardTitle>
                    <CardDescription>Autocomplete input and command palette with a list of suggestions</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Combobox
                                v-model="basicEmpty"
                                :options="countries"
                                placeholder="Select country..."
                                search-placeholder="Search countries..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Combobox
                                v-model="basicFilled"
                                :options="countries"
                                placeholder="Select country..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Combobox
                                v-model="basicInvalid"
                                :options="countries"
                                placeholder="Select country..."
                                invalid
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                            <Combobox
                                v-model="basicClearable"
                                :options="countries"
                                placeholder="Select country..."
                                clearable
                                fluid
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Combobox
                                v-model="basicDisabled"
                                :options="countries"
                                placeholder="Select country..."
                                disabled
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
                    <CardDescription>Select multiple values from the list</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-5 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Combobox
                                v-model="multiEmpty"
                                :options="techOptions"
                                placeholder="Select technologies..."
                                multiple
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Values</div>
                            <Combobox
                                v-model="multiFilled"
                                :options="techOptions"
                                placeholder="Select technologies..."
                                multiple
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <Combobox
                                v-model="multiInvalid"
                                :options="techOptions"
                                placeholder="Select technologies..."
                                multiple
                                invalid
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Many Selected</div>
                            <Combobox
                                v-model="multiMany"
                                :options="techOptions"
                                placeholder="Select technologies..."
                                multiple
                                fluid
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Combobox
                                v-model="multiDisabled"
                                :options="techOptions"
                                placeholder="Select technologies..."
                                multiple
                                disabled
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Async Search -->
            <Card>
                <CardHeader>
                    <CardTitle>Async Search</CardTitle>
                    <CardDescription>Fetch results from an API as the user types in the search box</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Server-side Search</div>
                            <Combobox
                                v-model="asyncValue"
                                :options="asyncOptions"
                                :loading="asyncLoading"
                                placeholder="Select country..."
                                search-placeholder="Type to search countries..."
                                empty-text="Type to search for countries..."
                                loading-text="Searching..."
                                disable-filter
                                fluid
                                @search="handleAsyncSearch"
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Pre-loaded Value</div>
                            <Combobox
                                v-model="asyncPreloaded"
                                :options="asyncPreloadedOptions"
                                :loading="asyncPreloadedLoading"
                                option-label="name"
                                option-value="id"
                                placeholder="Select user..."
                                search-placeholder="Search users..."
                                empty-text="Type to search for users..."
                                loading-text="Searching users..."
                                disable-filter
                                fluid
                                @search="handleAsyncPreloadedSearch"
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Custom Debounce (500ms)</div>
                            <Combobox
                                v-model="asyncDebounce"
                                :options="asyncDebounceOptions"
                                :loading="asyncDebounceLoading"
                                :debounce="500"
                                placeholder="Select technology..."
                                search-placeholder="Search (500ms debounce)..."
                                empty-text="Type to search..."
                                loading-text="Searching..."
                                disable-filter
                                fluid
                                @search="handleAsyncDebounceSearch"
                            />
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-muted rounded-md text-sm">
                        <p class="font-medium mb-1">How async search works:</p>
                        <ul class="list-disc list-inside text-muted-foreground space-y-1">
                            <li>Listen to <code class="bg-background px-1 rounded">@search</code> event to fetch data from your API</li>
                            <li>Use <code class="bg-background px-1 rounded">:loading="true"</code> while the request is in progress</li>
                            <li>Add <code class="bg-background px-1 rounded">disable-filter</code> to skip client-side filtering (server already filtered)</li>
                            <li>Adjust <code class="bg-background px-1 rounded">:debounce</code> to control request frequency (default: 300ms)</li>
                            <li>Update <code class="bg-background px-1 rounded">:options</code> with the fetched results</li>
                        </ul>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Option Template -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Option Template</CardTitle>
                    <CardDescription>Customize how options are rendered in the dropdown</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Description</div>
                            <Combobox
                                v-model="customValue"
                                :options="userOptions"
                                option-label="name"
                                option-value="id"
                                placeholder="Select user..."
                                search-placeholder="Search users..."
                                fluid
                            >
                                <template #option="{ option }">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ option.name }}</span>
                                        <span class="text-xs text-muted-foreground">{{ option.email }}</span>
                                    </div>
                                </template>
                            </Combobox>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Avatar</div>
                            <Combobox
                                v-model="avatarValue"
                                :options="userOptions"
                                option-label="name"
                                option-value="id"
                                placeholder="Select user..."
                                search-placeholder="Search users..."
                                fluid
                            >
                                <template #option="{ option }">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-xs font-medium">
                                            {{ (option as unknown as { name: string }).name.charAt(0) }}
                                        </div>
                                        <span>{{ (option as unknown as { name: string }).name }}</span>
                                    </div>
                                </template>
                            </Combobox>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Status</div>
                            <Combobox
                                v-model="statusValue"
                                :options="statusOptions"
                                option-label="label"
                                option-value="value"
                                placeholder="Select status..."
                                search-placeholder="Search..."
                                fluid
                            >
                                <template #option="{ option }">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-2 h-2 rounded-full"
                                            :class="{
                                                'bg-green-500': option.color === 'green',
                                                'bg-yellow-500': option.color === 'yellow',
                                                'bg-red-500': option.color === 'red',
                                                'bg-gray-500': option.color === 'gray',
                                            }"
                                        />
                                        <span>{{ option.label }}</span>
                                    </div>
                                </template>
                            </Combobox>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Combobox } from '@/components/ui/combobox';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Options (defined first to be used in refs)
const techOptions = [
    { label: 'Vue.js', value: 'vue' },
    { label: 'React', value: 'react' },
    { label: 'TypeScript', value: 'typescript' },
    { label: 'JavaScript', value: 'javascript' },
    { label: 'Tailwind CSS', value: 'tailwind' },
    { label: 'Node.js', value: 'node' },
    { label: 'Python', value: 'python' },
    { label: 'Go', value: 'go' },
    { label: 'Rust', value: 'rust' },
];

const userOptions = [
    { id: 1, name: 'John Doe', email: 'john@example.com' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
    { id: 3, name: 'Bob Johnson', email: 'bob@example.com' },
    { id: 4, name: 'Alice Williams', email: 'alice@example.com' },
    { id: 5, name: 'Charlie Brown', email: 'charlie@example.com' },
];

const statusOptions = [
    { label: 'Active', value: 'active', color: 'green' },
    { label: 'Pending', value: 'pending', color: 'yellow' },
    { label: 'Inactive', value: 'inactive', color: 'red' },
    { label: 'Draft', value: 'draft', color: 'gray' },
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

// Basic Combobox
const basicEmpty = ref('');
const basicFilled = ref('us');
const basicInvalid = ref('ca');
const basicClearable = ref('mx');
const basicDisabled = ref('br');

// Multi Select
const multiEmpty = ref<string[]>([]);
const multiFilled = ref<string[]>(['vue', 'typescript']);
const multiInvalid = ref<string[]>(['react']);
const multiMany = ref<string[]>(['vue', 'react', 'typescript', 'tailwind']);
const multiDisabled = ref<string[]>(['vue', 'react']);

// Async Search - start with empty options to demonstrate fetching
const asyncValue = ref('');
const asyncOptions = ref<{ label: string; value: string }[]>([]);
const asyncLoading = ref(false);

// Pre-loaded example: only include the selected option initially
const asyncPreloaded = ref(1);
const asyncPreloadedOptions = ref([
    { id: 1, name: 'John Doe', email: 'john@example.com' },
]);
const asyncPreloadedLoading = ref(false);

const asyncDebounce = ref('');
const asyncDebounceOptions = ref<{ label: string; value: string }[]>([]);
const asyncDebounceLoading = ref(false);

const handleAsyncSearch = async (query: string) => {
    // Only fetch if there's a search query
    if (!query) {
        asyncOptions.value = [];
        return;
    }

    asyncLoading.value = true;
    await new Promise(resolve => setTimeout(resolve, 800));

    // Simulate API response
    asyncOptions.value = countries
        .filter(c => c.label.toLowerCase().includes(query.toLowerCase()))
        .slice(0, 8);

    asyncLoading.value = false;
};

const handleAsyncPreloadedSearch = async (query: string) => {
    if (!query) {
        // Reset to just the selected option when cleared
        asyncPreloadedOptions.value = userOptions.filter(u => u.id === asyncPreloaded.value);
        return;
    }

    asyncPreloadedLoading.value = true;
    await new Promise(resolve => setTimeout(resolve, 600));

    asyncPreloadedOptions.value = userOptions
        .filter(u => u.name.toLowerCase().includes(query.toLowerCase()));

    asyncPreloadedLoading.value = false;
};

const handleAsyncDebounceSearch = async (query: string) => {
    if (!query) {
        asyncDebounceOptions.value = [];
        return;
    }

    asyncDebounceLoading.value = true;
    await new Promise(resolve => setTimeout(resolve, 800));

    asyncDebounceOptions.value = techOptions
        .filter(t => t.label.toLowerCase().includes(query.toLowerCase()));

    asyncDebounceLoading.value = false;
};

// Custom Templates
const customValue = ref('');
const avatarValue = ref('');
const statusValue = ref('');
</script>
