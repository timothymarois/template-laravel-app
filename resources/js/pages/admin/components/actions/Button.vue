<template>
    <LayoutApp
        title="Components - Button"
        pageTitle="Actions"
        :pageNavItems="sideNavItems"
        :pageTabs="actionsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Button</CardTitle>
                    <CardDescription>Clickable elements for user actions</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Variants</h4>
                        <div class="flex flex-wrap gap-2">
                            <Button variant="default">Default</Button>
                            <Button variant="secondary">Secondary</Button>
                            <Button variant="destructive">Destructive</Button>
                            <Button variant="outline">Outline</Button>
                            <Button variant="ghost">Ghost</Button>
                            <Button variant="link">Link</Button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Sizes</h4>
                        <div class="flex items-center gap-2">
                            <Button size="sm">Small</Button>
                            <Button size="default">Default</Button>
                            <Button size="lg">Large</Button>
                            <Button size="icon"><Plus class="size-4" /></Button>
                            <Button size="icon-sm"><Plus class="size-4" /></Button>
                            <Button size="icon-lg"><Plus class="size-4" /></Button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Icons</h4>
                        <div class="flex flex-wrap gap-2">
                            <Button>
                                <Plus class="size-4" />
                                Add Item
                            </Button>
                            <Button variant="outline">
                                <Download class="size-4" />
                                Download
                            </Button>
                            <Button variant="secondary">
                                Settings
                                <Settings class="size-4" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Button States</CardTitle>
                    <CardDescription>Interactive states - hover and click to see changes</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States Across Variants</h4>
                        <div class="grid grid-cols-6 gap-4 text-center text-xs text-muted-foreground mb-2">
                            <div>Default</div>
                            <div>Secondary</div>
                            <div>Destructive</div>
                            <div>Outline</div>
                            <div>Ghost</div>
                            <div>Link</div>
                        </div>
                        <div class="space-y-2">
                            <div class="grid grid-cols-6 gap-4">
                                <Button variant="default">Normal</Button>
                                <Button variant="secondary">Normal</Button>
                                <Button variant="destructive">Normal</Button>
                                <Button variant="outline">Normal</Button>
                                <Button variant="ghost">Normal</Button>
                                <Button variant="link">Normal</Button>
                            </div>
                            <div class="grid grid-cols-6 gap-4">
                                <Button variant="default" disabled>Disabled</Button>
                                <Button variant="secondary" disabled>Disabled</Button>
                                <Button variant="destructive" disabled>Disabled</Button>
                                <Button variant="outline" disabled>Disabled</Button>
                                <Button variant="ghost" disabled>Disabled</Button>
                                <Button variant="link" disabled>Disabled</Button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Loading State</h4>
                        <div class="flex gap-2">
                            <Button :disabled="isLoading" @click="simulateLoading">
                                <Loader2 v-if="isLoading" class="size-4 animate-spin" />
                                <span v-else>Click to Load</span>
                                <span v-if="isLoading">Loading...</span>
                            </Button>
                            <Button variant="outline" disabled>
                                <Loader2 class="size-4 animate-spin" />
                                Processing...
                            </Button>
                            <Button variant="secondary" disabled>
                                <Loader2 class="size-4 animate-spin" />
                                Saving...
                            </Button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Focus States (Tab or Click)</h4>
                        <p class="text-sm text-muted-foreground mb-2">Click or tab to these buttons to see focus ring</p>
                        <div class="flex gap-2">
                            <Button variant="default">Focus me</Button>
                            <Button variant="outline">Focus me</Button>
                            <Button variant="ghost">Focus me</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Button Groups</CardTitle>
                    <CardDescription>Patterns for grouping related actions</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Connected Button Group</h4>
                        <div class="inline-flex rounded-md shadow-sm">
                            <Button variant="outline" class="rounded-r-none">Left</Button>
                            <Button variant="outline" class="rounded-none border-l-0">Middle</Button>
                            <Button variant="outline" class="rounded-l-none border-l-0">Right</Button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Segmented Control</h4>
                        <div class="inline-flex rounded-md bg-muted p-1">
                            <Button
                                v-for="view in viewOptions"
                                :key="view.value"
                                :variant="selectedView === view.value ? 'default' : 'ghost'"
                                size="sm"
                                @click="selectedView = view.value"
                            >
                                <component :is="view.icon" class="size-4 mr-1" />
                                {{ view.label }}
                            </Button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Split Button</h4>
                        <div class="inline-flex">
                            <Button class="rounded-r-none">Save</Button>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="default" class="rounded-l-none border-l border-l-primary-foreground/20 px-2">
                                        <ChevronDown class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem>Save as Draft</DropdownMenuItem>
                                    <DropdownMenuItem>Save and Close</DropdownMenuItem>
                                    <DropdownMenuItem>Save as Template</DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Spaced Button Group</h4>
                        <div class="flex gap-2">
                            <Button variant="outline">Cancel</Button>
                            <Button variant="outline">Save Draft</Button>
                            <Button>Publish</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Plus,
    Download,
    Settings,
    ChevronDown,
    Loader2,
    LayoutGrid,
    List,
    Table2,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, actionsTabs } = useShowcaseNav();

const isLoading = ref(false);
const simulateLoading = () => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, 2000);
};

const selectedView = ref('grid');
const viewOptions = [
    { label: 'Grid', value: 'grid', icon: LayoutGrid },
    { label: 'List', value: 'list', icon: List },
    { label: 'Table', value: 'table', icon: Table2 },
];
</script>
