<template>
    <LayoutApp
        title="Components - Pie Charts"
        pageTitle="Pie & Donut Charts"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <!-- Simple Pie Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Simple Pie Chart</CardTitle>
                        <CardDescription>Basic pie chart for showing proportions</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 mb-4">
                            <div v-for="(item, index) in pieDataDisplay" :key="item.category" class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" :style="{ background: pieColors[index] }"></div>
                                <span class="text-sm">{{ item.category }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <VisSingleContainer :data="pieData" :style="{ width: '240px', height: '240px' }">
                                <VisDonut
                                    :value="(d: any) => d.value"
                                    :color="pieColors"
                                    :arcWidth="0"
                                    :padAngle="0.02"
                                />
                                <ChartDonutTooltip valueKey="value" />
                            </VisSingleContainer>
                        </div>
                    </CardContent>
                </Card>

                <!-- Donut Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Donut Chart</CardTitle>
                        <CardDescription>Pie chart with center cutout</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 mb-4">
                            <div v-for="(item, index) in donutDataDisplay" :key="item.browser" class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" :style="{ background: donutColors[index] }"></div>
                                <span class="text-sm">{{ item.browser }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <VisSingleContainer :data="donutData" :style="{ width: '240px', height: '240px' }">
                                <VisDonut
                                    :value="(d: any) => d.visitors"
                                    :color="donutColors"
                                    :arcWidth="40"
                                    :padAngle="0.02"
                                    :cornerRadius="4"
                                />
                                <ChartDonutTooltip valueKey="visitors" />
                            </VisSingleContainer>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Donut with Center Label -->
            <Card>
                <CardHeader>
                    <CardTitle>Donut with Center Label</CardTitle>
                    <CardDescription>Display total or key metric in the center</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-8">
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <VisSingleContainer :data="salesData" :style="{ width: '180px', height: '180px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="salesColors"
                                        :arcWidth="30"
                                        :padAngle="0.02"
                                        :cornerRadius="4"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold">$12.4k</span>
                                    <span class="text-sm text-muted-foreground">Total Sales</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-center gap-4 mt-4">
                                <div v-for="(item, index) in salesData" :key="item.name" class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full" :style="{ background: salesColors[index] }"></div>
                                    <span class="text-xs">{{ item.name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <VisSingleContainer :data="storageData" :style="{ width: '180px', height: '180px' }">
                                    <VisDonut
                                        :value="(d: any) => d.size"
                                        :color="storageColors"
                                        :arcWidth="30"
                                        :padAngle="0.02"
                                        :cornerRadius="4"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold">68%</span>
                                    <span class="text-sm text-muted-foreground">Used</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-center gap-4 mt-4">
                                <div v-for="(item, index) in storageData" :key="item.type" class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full" :style="{ background: storageColors[index] }"></div>
                                    <span class="text-xs">{{ item.type }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <VisSingleContainer :data="progressData" :style="{ width: '180px', height: '180px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="progressColors"
                                        :arcWidth="30"
                                        :padAngle="0"
                                        :cornerRadius="4"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold">73%</span>
                                    <span class="text-sm text-muted-foreground">Complete</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-center gap-4 mt-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                                    <span class="text-xs">Completed</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-muted"></div>
                                    <span class="text-xs">Remaining</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-2 gap-4">
                <!-- Semi-circle Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Semi-circle Chart</CardTitle>
                        <CardDescription>Half donut for gauge-like displays</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-col items-center justify-center py-4">
                            <div class="relative">
                                <VisSingleContainer :data="gaugeData" :style="{ width: '200px', height: '100px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="gaugeColors"
                                        :arcWidth="30"
                                        :angleRange="[-Math.PI / 2, Math.PI / 2]"
                                        :padAngle="0.02"
                                        :cornerRadius="4"
                                    />
                                </VisSingleContainer>
                                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 text-center pointer-events-none">
                                    <span class="text-2xl font-bold">85%</span>
                                    <p class="text-sm text-muted-foreground">Performance</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Nested Donut -->
                <Card>
                    <CardHeader>
                        <CardTitle>Nested Donuts</CardTitle>
                        <CardDescription>Multiple rings for comparison</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-primary"></div>
                                <span class="text-sm">This Year</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-2))"></div>
                                <span class="text-sm">Last Year</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center relative" style="height: 180px;">
                            <div class="absolute">
                                <VisSingleContainer :data="nestedOuterData" :style="{ width: '180px', height: '180px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="['hsl(var(--primary))', 'hsl(var(--muted))']"
                                        :arcWidth="20"
                                        :padAngle="0.02"
                                        :cornerRadius="4"
                                    />
                                </VisSingleContainer>
                            </div>
                            <div class="absolute">
                                <VisSingleContainer :data="nestedInnerData" :style="{ width: '120px', height: '120px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="['hsl(var(--chart-2))', 'hsl(var(--muted))']"
                                        :arcWidth="20"
                                        :padAngle="0.02"
                                        :cornerRadius="4"
                                    />
                                </VisSingleContainer>
                            </div>
                            <div class="text-center pointer-events-none z-10">
                                <span class="text-xl font-bold">+15%</span>
                                <p class="text-xs text-muted-foreground">Growth</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Mini Donuts in Grid -->
            <Card>
                <CardHeader>
                    <CardTitle>Category Distribution</CardTitle>
                    <CardDescription>Mini donut charts for quick metric overview</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-6">
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <VisSingleContainer :data="[{ value: 75 }, { value: 25 }]" :style="{ width: '64px', height: '64px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="['hsl(var(--primary))', 'hsl(var(--muted))']"
                                        :arcWidth="8"
                                        :cornerRadius="2"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="text-sm font-semibold">75%</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">Electronics</p>
                                <p class="text-sm text-muted-foreground">$12,450</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <VisSingleContainer :data="[{ value: 58 }, { value: 42 }]" :style="{ width: '64px', height: '64px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="['hsl(var(--chart-2))', 'hsl(var(--muted))']"
                                        :arcWidth="8"
                                        :cornerRadius="2"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="text-sm font-semibold">58%</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">Clothing</p>
                                <p class="text-sm text-muted-foreground">$8,320</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <VisSingleContainer :data="[{ value: 42 }, { value: 58 }]" :style="{ width: '64px', height: '64px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="['hsl(var(--chart-3))', 'hsl(var(--muted))']"
                                        :arcWidth="8"
                                        :cornerRadius="2"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="text-sm font-semibold">42%</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">Home</p>
                                <p class="text-sm text-muted-foreground">$5,890</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <VisSingleContainer :data="[{ value: 89 }, { value: 11 }]" :style="{ width: '64px', height: '64px' }">
                                    <VisDonut
                                        :value="(d: any) => d.value"
                                        :color="['hsl(var(--chart-4))', 'hsl(var(--muted))']"
                                        :arcWidth="8"
                                        :cornerRadius="2"
                                    />
                                </VisSingleContainer>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="text-sm font-semibold">89%</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">Books</p>
                                <p class="text-sm text-muted-foreground">$3,210</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Interactive Pie Chart with Clickable Legend -->
            <Card>
                <CardHeader>
                    <CardTitle>Traffic Sources</CardTitle>
                    <CardDescription>Click legend items to show/hide data segments</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="flex items-center justify-center">
                            <VisSingleContainer :data="filteredTrafficData" :style="{ width: '280px', height: '280px' }">
                                <VisDonut
                                    :value="(d: any) => d.visitors"
                                    :color="filteredTrafficColors"
                                    :arcWidth="50"
                                    :padAngle="0.02"
                                    :cornerRadius="4"
                                />
                                <ChartDonutTooltip valueKey="visitors" />
                            </VisSingleContainer>
                        </div>
                        <div class="flex flex-col justify-center space-y-3">
                            <div
                                v-for="(item, index) in trafficData"
                                :key="item.source"
                                class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all"
                                :class="{
                                    'opacity-40 bg-muted/50': hiddenSources.has(item.source),
                                    'hover:bg-muted/30': !hiddenSources.has(item.source)
                                }"
                                @click="toggleSource(item.source)"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-3 h-3 rounded-full transition-opacity"
                                        :style="{ background: trafficColors[index] }"
                                        :class="{ 'opacity-40': hiddenSources.has(item.source) }"
                                    ></div>
                                    <span class="font-medium" :class="{ 'line-through': hiddenSources.has(item.source) }">{{ item.source }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold">{{ item.visitors.toLocaleString() }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground text-center mt-2">Click items to toggle visibility</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { VisSingleContainer, VisDonut } from '@unovis/vue';
import { ChartDonutTooltip } from '@/components/ui/chart';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Color palettes
const pieColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))', 'hsl(var(--chart-4))'];
const donutColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))', 'hsl(var(--chart-4))', 'hsl(var(--chart-5))'];
const salesColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))'];
const storageColors = ['hsl(var(--chart-1))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))', 'hsl(var(--muted))'];
const progressColors = ['hsl(var(--primary))', 'hsl(var(--muted))'];
const gaugeColors = ['hsl(var(--primary))', 'hsl(var(--muted))'];
const trafficColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))', 'hsl(var(--chart-4))', 'hsl(var(--chart-5))'];

// Pie chart data (with name field for tooltip)
const pieData = [
    { name: 'Electronics', value: 400 },
    { name: 'Clothing', value: 300 },
    { name: 'Home', value: 200 },
    { name: 'Other', value: 100 },
];

// For backwards compatibility with template
const pieDataDisplay = pieData.map(d => ({ category: d.name, value: d.value }));

// Donut chart data (with name field for tooltip)
const donutData = [
    { name: 'Chrome', visitors: 275 },
    { name: 'Safari', visitors: 200 },
    { name: 'Firefox', visitors: 187 },
    { name: 'Edge', visitors: 173 },
    { name: 'Other', visitors: 90 },
];

// For backwards compatibility with template
const donutDataDisplay = donutData.map(d => ({ browser: d.name, visitors: d.visitors }));

// Sales data
const salesData = [
    { name: 'Online', value: 5200 },
    { name: 'Retail', value: 4800 },
    { name: 'Wholesale', value: 2400 },
];

// Storage data
const storageData = [
    { type: 'Documents', size: 35 },
    { type: 'Media', size: 45 },
    { type: 'Apps', size: 12 },
    { type: 'Free', size: 8 },
];

// Progress data (for progress ring)
const progressData = [
    { value: 73 },
    { value: 27 },
];

// Gauge data
const gaugeData = [
    { value: 85 },
    { value: 15 },
];

// Nested donut data
const nestedOuterData = [
    { value: 78 },
    { value: 22 },
];

const nestedInnerData = [
    { value: 63 },
    { value: 37 },
];

// Traffic data with interactive legend (name field for tooltip)
const trafficData = [
    { name: 'Organic Search', source: 'Organic Search', visitors: 45231 },
    { name: 'Direct', source: 'Direct', visitors: 32150 },
    { name: 'Social Media', source: 'Social Media', visitors: 18420 },
    { name: 'Referral', source: 'Referral', visitors: 12340 },
    { name: 'Email', source: 'Email', visitors: 8920 },
];

// Track which traffic sources are visible
const hiddenSources = ref<Set<string>>(new Set());

const toggleSource = (source: string) => {
    if (hiddenSources.value.has(source)) {
        hiddenSources.value.delete(source);
    } else {
        // Don't hide if it's the last visible item
        if (hiddenSources.value.size < trafficData.length - 1) {
            hiddenSources.value.add(source);
        }
    }
    // Force reactivity
    hiddenSources.value = new Set(hiddenSources.value);
};

const filteredTrafficData = computed(() =>
    trafficData.filter(item => !hiddenSources.value.has(item.source))
);

const filteredTrafficColors = computed(() =>
    trafficData
        .map((item, index) => ({ item, color: trafficColors[index] }))
        .filter(({ item }) => !hiddenSources.value.has(item.source))
        .map(({ color }) => color)
);

const totalTraffic = computed(() => filteredTrafficData.value.reduce((sum, item) => sum + item.visitors, 0));
</script>
