<template>
    <LayoutApp
        title="Components - Loading"
        pageTitle="Loading"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Spinner Sizes -->
            <Card>
                <CardHeader>
                    <CardTitle>Spinner Sizes</CardTitle>
                    <CardDescription>Different spinner sizes for various contexts</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-6">
                        <div class="flex flex-col items-center gap-2">
                            <Spinner size="xs" />
                            <span class="text-xs text-muted-foreground">xs</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner size="sm" />
                            <span class="text-xs text-muted-foreground">sm</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner size="default" />
                            <span class="text-xs text-muted-foreground">default</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner size="lg" />
                            <span class="text-xs text-muted-foreground">lg</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner size="xl" />
                            <span class="text-xs text-muted-foreground">xl</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Spinner Variants -->
            <Card>
                <CardHeader>
                    <CardTitle>Spinner Variants</CardTitle>
                    <CardDescription>Color variants for different backgrounds</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-6">
                        <div class="flex flex-col items-center gap-2">
                            <Spinner variant="default" />
                            <span class="text-xs text-muted-foreground">default</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner variant="primary" />
                            <span class="text-xs text-muted-foreground">primary</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner variant="secondary" />
                            <span class="text-xs text-muted-foreground">secondary</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <Spinner variant="muted" />
                            <span class="text-xs text-muted-foreground">muted</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 bg-primary rounded-lg">
                            <Spinner class="text-primary-foreground" />
                            <span class="text-xs text-primary-foreground/70">on dark</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Spinner in Context -->
            <Card>
                <CardHeader>
                    <CardTitle>Spinner in Context</CardTitle>
                    <CardDescription>Using spinners with buttons and overlays</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex gap-3">
                        <Button :loading="true">
                            Loading...
                        </Button>
                        <Button variant="outline" :loading="true">
                            Processing
                        </Button>
                        <Button variant="secondary" :loading="true">
                            Saving
                        </Button>
                    </div>
                    <div class="flex gap-3">
                        <Button size="sm" :loading="true">Small</Button>
                        <Button :loading="true">Default</Button>
                        <Button size="lg" :loading="true">Large</Button>
                    </div>
                    <div class="relative p-8 border rounded-lg">
                        <div class="absolute inset-0 bg-background/80 flex items-center justify-center rounded-lg">
                            <div class="flex flex-col items-center gap-2">
                                <Spinner size="lg" variant="primary" />
                                <span class="text-sm text-muted-foreground">Loading content...</span>
                            </div>
                        </div>
                        <p class="text-muted-foreground">This content is being loaded...</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Progress Basic -->
            <Card>
                <CardHeader>
                    <CardTitle>Progress Bar</CardTitle>
                    <CardDescription>Show completion status with progress bars</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="space-y-3">
                        <Progress :model-value="0" />
                        <Progress :model-value="25" />
                        <Progress :model-value="50" />
                        <Progress :model-value="75" />
                        <Progress :model-value="100" />
                    </div>
                </CardContent>
            </Card>

            <!-- Progress Sizes -->
            <Card>
                <CardHeader>
                    <CardTitle>Progress Sizes</CardTitle>
                    <CardDescription>Different bar heights for various contexts</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Small</span>
                        <Progress :model-value="60" size="sm" />
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Default</span>
                        <Progress :model-value="60" size="default" />
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Large</span>
                        <Progress :model-value="60" size="lg" />
                    </div>
                </CardContent>
            </Card>

            <!-- Progress Variants -->
            <Card>
                <CardHeader>
                    <CardTitle>Progress Variants</CardTitle>
                    <CardDescription>Semantic colors for different states</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Default</span>
                        <Progress :model-value="70" variant="default" />
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Success</span>
                        <Progress :model-value="100" variant="success" />
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Warning</span>
                        <Progress :model-value="45" variant="warning" />
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Destructive</span>
                        <Progress :model-value="85" variant="destructive" />
                    </div>
                </CardContent>
            </Card>

            <!-- Progress with Value -->
            <Card>
                <CardHeader>
                    <CardTitle>Progress with Value</CardTitle>
                    <CardDescription>Display the percentage alongside the bar</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Progress :model-value="uploadProgress" show-value>
                        <template #label>
                            <span class="font-medium">Uploading files...</span>
                        </template>
                    </Progress>
                    <Progress :model-value="downloadProgress" show-value variant="success">
                        <template #label>
                            <span class="font-medium">Download complete</span>
                        </template>
                    </Progress>
                    <div class="flex gap-2">
                        <Button variant="outline" @click="simulateProgress">Simulate Progress</Button>
                        <Button variant="outline" @click="resetProgress">Reset</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Indeterminate Progress -->
            <Card>
                <CardHeader>
                    <CardTitle>Indeterminate Progress</CardTitle>
                    <CardDescription>For unknown duration operations</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Processing...</span>
                        <Progress indeterminate />
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium">Loading data...</span>
                        <Progress indeterminate variant="success" />
                    </div>
                </CardContent>
            </Card>

            <!-- Skeleton Loading -->
            <Card>
                <CardHeader>
                    <CardTitle>Skeleton</CardTitle>
                    <CardDescription>Placeholder loading states for content</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="space-y-3">
                        <Skeleton class="h-4 w-[250px]" />
                        <Skeleton class="h-4 w-[200px]" />
                        <Skeleton class="h-4 w-[300px]" />
                    </div>

                    <div class="flex items-center space-x-4">
                        <Skeleton class="h-12 w-12 rounded-full" />
                        <div class="space-y-2">
                            <Skeleton class="h-4 w-[200px]" />
                            <Skeleton class="h-3 w-[150px]" />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Skeleton class="h-[125px] w-full rounded-xl" />
                        <div class="space-y-2">
                            <Skeleton class="h-4 w-full" />
                            <Skeleton class="h-4 w-4/5" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Skeleton Card Example -->
            <div class="grid grid-cols-2 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Loading Card</CardTitle>
                        <CardDescription>Skeleton placeholder for a card</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <Skeleton class="size-16 rounded-lg" />
                                <div class="space-y-2 flex-1">
                                    <Skeleton class="h-4 w-3/4" />
                                    <Skeleton class="h-3 w-1/2" />
                                </div>
                            </div>
                            <Skeleton class="h-20 w-full" />
                            <div class="flex gap-2">
                                <Skeleton class="h-9 w-20" />
                                <Skeleton class="h-9 w-20" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Loaded Card</CardTitle>
                        <CardDescription>What the loaded state looks like</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded-lg bg-primary/10 flex items-center justify-center">
                                    <Package class="size-8 text-primary" />
                                </div>
                                <div class="space-y-1 flex-1">
                                    <h4 class="font-medium">Product Name</h4>
                                    <p class="text-sm text-muted-foreground">Category</p>
                                </div>
                            </div>
                            <p class="text-sm text-muted-foreground">This is the description of the product that would appear once the content has loaded.</p>
                            <div class="flex gap-2">
                                <Button>View</Button>
                                <Button variant="outline">Edit</Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Table Skeleton -->
            <Card>
                <CardHeader>
                    <CardTitle>Table Skeleton</CardTitle>
                    <CardDescription>Loading state for data tables</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="border rounded-lg">
                        <div class="border-b p-4 flex gap-4">
                            <Skeleton class="h-4 w-[100px]" />
                            <Skeleton class="h-4 w-[150px]" />
                            <Skeleton class="h-4 w-[120px]" />
                            <Skeleton class="h-4 w-[80px]" />
                        </div>
                        <div v-for="i in 4" :key="i" class="border-b last:border-0 p-4 flex gap-4">
                            <Skeleton class="h-4 w-[100px]" />
                            <Skeleton class="h-4 w-[150px]" />
                            <Skeleton class="h-4 w-[120px]" />
                            <Skeleton class="h-4 w-[80px]" />
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
import { Spinner } from '@/components/ui/spinner';
import { Progress } from '@/components/ui/progress';
import { Skeleton } from '@/components/ui/skeleton';
import { Package } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const uploadProgress = ref(35);
const downloadProgress = ref(100);

let progressInterval: number | null = null;

const simulateProgress = () => {
    if (progressInterval) return;
    uploadProgress.value = 0;
    progressInterval = window.setInterval(() => {
        uploadProgress.value += Math.random() * 15;
        if (uploadProgress.value >= 100) {
            uploadProgress.value = 100;
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        }
    }, 500);
};

const resetProgress = () => {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    uploadProgress.value = 35;
};
</script>
