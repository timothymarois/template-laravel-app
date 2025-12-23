<template>
    <LayoutApp
        title="Components - Select"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Select</CardTitle>
                    <CardDescription>Dropdown selection menus</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Empty</div>
                                <Select v-model="emptySelect" :options="basicOptions" placeholder="Select option" class="w-full" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Value</div>
                                <Select v-model="filledSelect" :options="basicOptions" placeholder="Select option" class="w-full" />
                            </div>
                            <div class="opacity-50">
                                <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                                <SelectBase disabled>
                                    <SelectTrigger disabled class="w-full">
                                        <SelectValue placeholder="Disabled" />
                                    </SelectTrigger>
                                </SelectBase>
                            </div>
                            <div class="opacity-50">
                                <div class="text-xs text-muted-foreground mb-1">Disabled with Value</div>
                                <SelectBase v-model="disabledSelect" disabled>
                                    <SelectTrigger disabled class="w-full">
                                        <SelectValue placeholder="Select option" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="opt in basicOptions" :key="opt.value" :value="opt.value">
                                            {{ opt.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </SelectBase>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Sizes</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Small</div>
                                <Select v-model="sizeSelect" :options="basicOptions" size="small" placeholder="Small" class="w-full" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Default</div>
                                <Select v-model="sizeSelect" :options="basicOptions" placeholder="Default" class="w-full" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Large</div>
                                <Select v-model="sizeSelect" :options="basicOptions" size="large" placeholder="Large" class="w-full" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Advanced Select</CardTitle>
                    <CardDescription>Grouped items, scrollable lists, and search</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Empty</div>
                                <Popover v-model:open="advancedEmptyOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span :class="advancedEmptyValue ? '' : 'text-muted-foreground'">
                                                {{ advancedEmptyLabel || 'Select option...' }}
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-0">
                                        <div class="max-h-60 overflow-y-auto p-1">
                                            <div
                                                v-for="country in countries"
                                                :key="country.value"
                                                class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                                @click="selectAdvancedEmpty(country.value)"
                                            >
                                                <Check
                                                    class="mr-2 h-4 w-4"
                                                    :class="advancedEmptyValue === country.value ? 'opacity-100' : 'opacity-0'"
                                                />
                                                {{ country.label }}
                                            </div>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Value</div>
                                <Popover v-model:open="advancedFilledOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span :class="advancedFilledValue ? '' : 'text-muted-foreground'">
                                                {{ advancedFilledLabel || 'Select option...' }}
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-0">
                                        <div class="max-h-60 overflow-y-auto p-1">
                                            <div
                                                v-for="country in countries"
                                                :key="country.value"
                                                class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                                @click="selectAdvancedFilled(country.value)"
                                            >
                                                <Check
                                                    class="mr-2 h-4 w-4"
                                                    :class="advancedFilledValue === country.value ? 'opacity-100' : 'opacity-0'"
                                                />
                                                {{ country.label }}
                                            </div>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="opacity-50">
                                <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                                <button
                                    type="button"
                                    disabled
                                    class="inline-flex h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm cursor-not-allowed"
                                >
                                    <span>Canada</span>
                                    <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Examples</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Groups</div>
                                <SelectBase v-model="groupSelect">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select a fruit or vegetable" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Fruits</SelectLabel>
                                            <SelectItem value="apple">Apple</SelectItem>
                                            <SelectItem value="banana">Banana</SelectItem>
                                            <SelectItem value="orange">Orange</SelectItem>
                                        </SelectGroup>
                                        <SelectSeparator />
                                        <SelectGroup>
                                            <SelectLabel>Vegetables</SelectLabel>
                                            <SelectItem value="carrot">Carrot</SelectItem>
                                            <SelectItem value="broccoli">Broccoli</SelectItem>
                                            <SelectItem value="spinach">Spinach</SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </SelectBase>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Scrollbar</div>
                                <Popover v-model:open="countryOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span :class="countrySelectLabel ? '' : 'text-muted-foreground'">
                                                {{ countrySelectLabel || 'Select a country...' }}
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-0">
                                        <div class="max-h-60 overflow-y-auto p-1">
                                            <div
                                                v-for="country in countries"
                                                :key="country.value"
                                                class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                                @click="selectCountrySimple(country.value)"
                                            >
                                                <Check
                                                    class="mr-2 h-4 w-4"
                                                    :class="countrySelect === country.value ? 'opacity-100' : 'opacity-0'"
                                                />
                                                {{ country.label }}
                                            </div>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Searchable</div>
                                <Popover v-model:open="searchOpen" @update:open="(open: boolean) => !open && (searchQuery = '')">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            role="combobox"
                                            :aria-expanded="searchOpen"
                                            class="inline-flex h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span :class="selectedCountryLabel ? '' : 'text-muted-foreground'">
                                                {{ selectedCountryLabel || 'Search countries...' }}
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-0">
                                        <div class="flex items-center border-b px-3">
                                            <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                                            <input
                                                v-model="searchQuery"
                                                placeholder="Search countries..."
                                                class="flex h-10 w-full bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground"
                                            />
                                        </div>
                                        <div class="max-h-60 overflow-y-auto p-1">
                                            <div
                                                v-if="filteredCountries.length === 0"
                                                class="py-6 text-center text-sm text-muted-foreground"
                                            >
                                                No countries found.
                                            </div>
                                            <div
                                                v-for="country in filteredCountries"
                                                :key="country.value"
                                                class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                                @click="selectCountry(country.value)"
                                            >
                                                <Check
                                                    class="mr-2 h-4 w-4"
                                                    :class="searchableSelect === country.value ? 'opacity-100' : 'opacity-0'"
                                                />
                                                {{ country.label }}
                                            </div>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Multi Select</CardTitle>
                    <CardDescription>Select multiple items from a list</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Empty</div>
                                <Popover v-model:open="multiEmptyOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex min-h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1.5 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span :class="multiEmptySelected.length ? '' : 'text-muted-foreground'">
                                                {{ multiEmptySelected.length ? `${multiEmptySelected.length} selected` : 'Select options...' }}
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-1">
                                        <div
                                            v-for="option in multiOptions"
                                            :key="option.value"
                                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            @click="toggleMultiEmpty(option.value)"
                                        >
                                            <span class="mr-2 flex h-4 w-4 items-center justify-center">
                                                <Check v-if="multiEmptySelected.includes(option.value)" class="h-4 w-4" />
                                            </span>
                                            {{ option.label }}
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Tags</div>
                                <Popover v-model:open="multiFilledOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex min-h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1.5 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span class="flex flex-wrap gap-1 flex-1">
                                                <span
                                                    v-if="multiFilledSelected.length === 0"
                                                    class="text-muted-foreground"
                                                >
                                                    Select options...
                                                </span>
                                                <span
                                                    v-for="value in multiFilledSelected"
                                                    :key="value"
                                                    class="inline-flex items-center gap-1 rounded bg-secondary px-2 py-0.5 text-xs font-medium"
                                                >
                                                    {{ techOptions.find(t => t.value === value)?.label }}
                                                    <X
                                                        class="h-3 w-3 cursor-pointer hover:text-destructive"
                                                        @click.stop="removeMultiFilled(value)"
                                                    />
                                                </span>
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-1">
                                        <div
                                            v-for="option in techOptions"
                                            :key="option.value"
                                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            @click="toggleMultiFilled(option.value)"
                                        >
                                            <span class="mr-2 flex h-4 w-4 items-center justify-center">
                                                <Check v-if="multiFilledSelected.includes(option.value)" class="h-4 w-4" />
                                            </span>
                                            {{ option.label }}
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="opacity-50">
                                <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                                <button
                                    type="button"
                                    disabled
                                    class="inline-flex min-h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1.5 text-sm cursor-not-allowed"
                                >
                                    <span class="flex flex-wrap gap-1 flex-1">
                                        <span class="inline-flex items-center gap-1 rounded bg-secondary px-2 py-0.5 text-xs font-medium">
                                            TypeScript
                                        </span>
                                    </span>
                                    <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Examples</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Basic</div>
                                <Popover v-model:open="multiOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span :class="multiSelected.length ? '' : 'text-muted-foreground'">
                                                {{ multiSelectedLabel || 'Select options...' }}
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-1">
                                        <div
                                            v-for="option in multiOptions"
                                            :key="option.value"
                                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            @click="toggleMultiOption(option.value)"
                                        >
                                            <div class="mr-2 flex h-4 w-4 items-center justify-center">
                                                <Check
                                                    v-if="multiSelected.includes(option.value)"
                                                    class="h-4 w-4"
                                                />
                                            </div>
                                            {{ option.label }}
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Tags</div>
                                <Popover v-model:open="tagsOpen">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex min-h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1.5 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span class="flex flex-wrap gap-1 flex-1">
                                                <span
                                                    v-if="tagsSelected.length === 0"
                                                    class="text-muted-foreground"
                                                >
                                                    Select technologies...
                                                </span>
                                                <span
                                                    v-for="value in tagsSelected"
                                                    :key="value"
                                                    class="inline-flex items-center gap-1 rounded bg-secondary px-2 py-0.5 text-xs font-medium"
                                                >
                                                    {{ techOptions.find(t => t.value === value)?.label }}
                                                    <X
                                                        class="h-3 w-3 cursor-pointer hover:text-destructive"
                                                        @click.stop="removeTag(value)"
                                                    />
                                                </span>
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-1">
                                        <div
                                            v-for="option in techOptions"
                                            :key="option.value"
                                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            @click="toggleTagOption(option.value)"
                                        >
                                            <div class="mr-2 flex h-4 w-4 items-center justify-center">
                                                <Check
                                                    v-if="tagsSelected.includes(option.value)"
                                                    class="h-4 w-4"
                                                />
                                            </div>
                                            {{ option.label }}
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Searchable</div>
                                <Popover v-model:open="searchMultiOpen" @update:open="(open: boolean) => !open && (searchMultiQuery = '')">
                                    <PopoverTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex min-h-9 w-full items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1.5 text-sm transition-all cursor-pointer hover:border-foreground/50 focus:outline-none focus:border-foreground/50"
                                        >
                                            <span class="flex flex-wrap gap-1 flex-1">
                                                <span
                                                    v-if="searchMultiSelected.length === 0"
                                                    class="text-muted-foreground"
                                                >
                                                    Search and select countries...
                                                </span>
                                                <span
                                                    v-for="value in searchMultiSelected"
                                                    :key="value"
                                                    class="inline-flex items-center gap-1 rounded bg-secondary px-2 py-0.5 text-xs font-medium"
                                                >
                                                    {{ countries.find(c => c.value === value)?.label }}
                                                    <X
                                                        class="h-3 w-3 cursor-pointer hover:text-destructive"
                                                        @click.stop="removeSearchMulti(value)"
                                                    />
                                                </span>
                                            </span>
                                            <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
                                        </button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--reka-popover-trigger-width] p-0">
                                        <div class="flex items-center border-b px-3">
                                            <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                                            <input
                                                v-model="searchMultiQuery"
                                                placeholder="Search countries..."
                                                class="flex h-10 w-full bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground"
                                            />
                                        </div>
                                        <div class="max-h-60 overflow-y-auto p-1">
                                            <div
                                                v-if="filteredMultiCountries.length === 0"
                                                class="py-6 text-center text-sm text-muted-foreground"
                                            >
                                                No countries found.
                                            </div>
                                            <div
                                                v-for="country in filteredMultiCountries"
                                                :key="country.value"
                                                class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                                @click="toggleSearchMulti(country.value)"
                                            >
                                                <div class="mr-2 flex h-4 w-4 items-center justify-center">
                                                    <Check
                                                        v-if="searchMultiSelected.includes(country.value)"
                                                        class="h-4 w-4"
                                                    />
                                                </div>
                                                {{ country.label }}
                                            </div>
                                        </div>
                                    </PopoverContent>
                                </Popover>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Select } from '@/components/ui/form';
import {
    SelectBase,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectSeparator,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    PopoverBase as Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Check, ChevronsUpDown, Search, X } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();

// Basic Select
const emptySelect = ref('');
const filledSelect = ref('option2');
const disabledSelect = ref('option1');
const sizeSelect = ref('');
const groupSelect = ref('');

// Scrollable Select
const countryOpen = ref(false);
const countrySelect = ref('');

// Searchable Select
const searchableSelect = ref('');
const searchOpen = ref(false);
const searchQuery = ref('');

// Multi-Select Basic
const multiOpen = ref(false);
const multiSelected = ref<string[]>([]);

// Multi-Select Tags
const tagsOpen = ref(false);
const tagsSelected = ref<string[]>(['vue', 'typescript']);

// Multi-Select Searchable
const searchMultiOpen = ref(false);
const searchMultiSelected = ref<string[]>([]);
const searchMultiQuery = ref('');

// Advanced Select States
const advancedEmptyOpen = ref(false);
const advancedEmptyValue = ref('');
const advancedFilledOpen = ref(false);
const advancedFilledValue = ref('us');

// Multi-Select States
const multiEmptyOpen = ref(false);
const multiEmptySelected = ref<string[]>([]);
const multiFilledOpen = ref(false);
const multiFilledSelected = ref<string[]>(['vue', 'typescript']);

const basicOptions = [
    { label: 'Option 1', value: 'option1' },
    { label: 'Option 2', value: 'option2' },
    { label: 'Option 3', value: 'option3' },
];

const multiOptions = [
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

// Computed
const filteredCountries = computed(() => {
    if (!searchQuery.value) return countries;
    return countries.filter(c =>
        c.label.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const filteredMultiCountries = computed(() => {
    if (!searchMultiQuery.value) return countries;
    return countries.filter(c =>
        c.label.toLowerCase().includes(searchMultiQuery.value.toLowerCase())
    );
});

const countrySelectLabel = computed(() => {
    const country = countries.find(c => c.value === countrySelect.value);
    return country?.label || '';
});

const selectedCountryLabel = computed(() => {
    const country = countries.find(c => c.value === searchableSelect.value);
    return country?.label || '';
});

const multiSelectedLabel = computed(() => {
    if (multiSelected.value.length === 0) return '';
    if (multiSelected.value.length === 1) {
        return multiOptions.find(o => o.value === multiSelected.value[0])?.label;
    }
    return `${multiSelected.value.length} selected`;
});

const advancedEmptyLabel = computed(() => {
    const country = countries.find(c => c.value === advancedEmptyValue.value);
    return country?.label || '';
});

const advancedFilledLabel = computed(() => {
    const country = countries.find(c => c.value === advancedFilledValue.value);
    return country?.label || '';
});

// Methods
const selectCountrySimple = (value: string) => {
    countrySelect.value = value;
    countryOpen.value = false;
};

const selectCountry = (value: string) => {
    searchableSelect.value = value;
    searchOpen.value = false;
    searchQuery.value = '';
};

const toggleMultiOption = (value: string) => {
    const index = multiSelected.value.indexOf(value);
    if (index === -1) {
        multiSelected.value.push(value);
    } else {
        multiSelected.value.splice(index, 1);
    }
};

const toggleTagOption = (value: string) => {
    const index = tagsSelected.value.indexOf(value);
    if (index === -1) {
        tagsSelected.value.push(value);
    } else {
        tagsSelected.value.splice(index, 1);
    }
};

const removeTag = (value: string) => {
    const index = tagsSelected.value.indexOf(value);
    if (index !== -1) {
        tagsSelected.value.splice(index, 1);
    }
};

const toggleSearchMulti = (value: string) => {
    const index = searchMultiSelected.value.indexOf(value);
    if (index === -1) {
        searchMultiSelected.value.push(value);
    } else {
        searchMultiSelected.value.splice(index, 1);
    }
};

const removeSearchMulti = (value: string) => {
    const index = searchMultiSelected.value.indexOf(value);
    if (index !== -1) {
        searchMultiSelected.value.splice(index, 1);
    }
};

// Advanced Select State Methods
const selectAdvancedEmpty = (value: string) => {
    advancedEmptyValue.value = value;
    advancedEmptyOpen.value = false;
};

const selectAdvancedFilled = (value: string) => {
    advancedFilledValue.value = value;
    advancedFilledOpen.value = false;
};

// Multi Select State Methods
const toggleMultiEmpty = (value: string) => {
    const index = multiEmptySelected.value.indexOf(value);
    if (index === -1) {
        multiEmptySelected.value.push(value);
    } else {
        multiEmptySelected.value.splice(index, 1);
    }
};

const toggleMultiFilled = (value: string) => {
    const index = multiFilledSelected.value.indexOf(value);
    if (index === -1) {
        multiFilledSelected.value.push(value);
    } else {
        multiFilledSelected.value.splice(index, 1);
    }
};

const removeMultiFilled = (value: string) => {
    const index = multiFilledSelected.value.indexOf(value);
    if (index !== -1) {
        multiFilledSelected.value.splice(index, 1);
    }
};
</script>
