<template>
    <LayoutApp
        title="Components - Bar Charts"
        pageTitle="Bar Charts"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Simple Bar Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Simple Bar Chart</CardTitle>
                    <CardDescription>Basic vertical bar chart with single data series</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="simpleBarData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisGroupedBar
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.value"
                                :color="'hsl(var(--primary))'"
                                :roundedCorners="4"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" :tickFormat="(v: number) => `$${v/1000}k`" />
                            <VisCrosshair :template="simpleBarTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <!-- Grouped Bar Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Grouped Bar Chart</CardTitle>
                    <CardDescription>Compare multiple data series side by side</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span class="text-sm">Desktop</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-2))"></div>
                            <span class="text-sm">Mobile</span>
                        </div>
                    </div>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="groupedBarData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisGroupedBar
                                :x="(d: any) => d.month"
                                :y="[(d: any) => d.desktop, (d: any) => d.mobile]"
                                :color="groupedColors"
                                :roundedCorners="4"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" />
                            <VisCrosshair :template="groupedBarTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-2 gap-4">
                <!-- Horizontal Bar Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Horizontal Bar Chart</CardTitle>
                        <CardDescription>Bar chart with horizontal orientation</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-72 overflow-hidden">
                            <VisXYContainer :data="horizontalBarData" :margin="{ top: 10, right: 20, bottom: 30, left: 80 }">
                                <VisGroupedBar
                                    :x="(d: any) => d.value"
                                    :y="(d: any) => d.category"
                                    :color="'hsl(var(--primary))'"
                                    :roundedCorners="4"
                                    orientation="horizontal"
                                />
                                <VisAxis type="x" />
                                <VisAxis type="y" :tickFormat="(i: number) => categories[i]" />
                                <VisCrosshair :template="horizontalBarTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>

                <!-- Stacked Bar Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Stacked Bar Chart</CardTitle>
                        <CardDescription>Multiple series stacked on top of each other</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-primary"></div>
                                <span class="text-sm">Product A</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-2))"></div>
                                <span class="text-sm">Product B</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-3))"></div>
                                <span class="text-sm">Product C</span>
                            </div>
                        </div>
                        <div class="h-56 overflow-hidden">
                            <VisXYContainer :data="stackedBarData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisStackedBar
                                    :x="(d: any) => d.month"
                                    :y="[(d: any) => d.productA, (d: any) => d.productB, (d: any) => d.productC]"
                                    :color="stackedColors"
                                    :roundedCorners="4"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" />
                                <VisCrosshair :template="stackedBarTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Bar Chart with Negative Values -->
            <Card>
                <CardHeader>
                    <CardTitle>Bar Chart with Negative Values</CardTitle>
                    <CardDescription>Display profit and loss data</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="negativeBarData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisGroupedBar
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.profit"
                                :color="(d: any) => d.profit >= 0 ? 'hsl(var(--chart-1))' : 'hsl(var(--destructive))'"
                                :roundedCorners="4"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" :tickFormat="(v: number) => `$${v/1000}k`" />
                            <VisCrosshair :template="negativeBarTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <!-- Interactive Bar Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Interactive Bar Chart</CardTitle>
                    <CardDescription>Hover over bars to see values with tooltip</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span class="text-sm">Revenue</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-2))"></div>
                            <span class="text-sm">Expenses</span>
                        </div>
                    </div>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="interactiveData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisGroupedBar
                                :x="(d: any) => d.month"
                                :y="[(d: any) => d.revenue, (d: any) => d.expenses]"
                                :color="groupedColors"
                                :roundedCorners="4"
                                :barPadding="0.2"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" :tickFormat="(v: number) => `$${v/1000}k`" />
                            <VisCrosshair :template="crosshairTemplate" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <!-- Mini Bar Charts -->
            <div class="grid grid-cols-4 gap-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Revenue</CardDescription>
                        <CardTitle class="text-2xl">$45,231</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="h-16 overflow-hidden">
                            <VisXYContainer :data="miniBarData1" :margin="{ top: 0, right: 0, bottom: 0, left: 0 }">
                                <VisGroupedBar
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--primary))'"
                                    :roundedCorners="2"
                                />
                            </VisXYContainer>
                        </div>
                        <p class="text-xs text-muted-foreground mt-2">+20.1% from last month</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Active Users</CardDescription>
                        <CardTitle class="text-2xl">+2,350</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="h-16 overflow-hidden">
                            <VisXYContainer :data="miniBarData2" :margin="{ top: 0, right: 0, bottom: 0, left: 0 }">
                                <VisGroupedBar
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-2))'"
                                    :roundedCorners="2"
                                />
                            </VisXYContainer>
                        </div>
                        <p class="text-xs text-muted-foreground mt-2">+180.1% from last month</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>New Orders</CardDescription>
                        <CardTitle class="text-2xl">+12,234</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="h-16 overflow-hidden">
                            <VisXYContainer :data="miniBarData3" :margin="{ top: 0, right: 0, bottom: 0, left: 0 }">
                                <VisGroupedBar
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-3))'"
                                    :roundedCorners="2"
                                />
                            </VisXYContainer>
                        </div>
                        <p class="text-xs text-muted-foreground mt-2">+19% from last month</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Conversion Rate</CardDescription>
                        <CardTitle class="text-2xl">3.2%</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="h-16 overflow-hidden">
                            <VisXYContainer :data="miniBarData4" :margin="{ top: 0, right: 0, bottom: 0, left: 0 }">
                                <VisGroupedBar
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-4))'"
                                    :roundedCorners="2"
                                />
                            </VisXYContainer>
                        </div>
                        <p class="text-xs text-muted-foreground mt-2">+4.1% from last month</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { VisXYContainer, VisGroupedBar, VisStackedBar, VisAxis, VisCrosshair, VisTooltip } from '@unovis/vue';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
const categories = ['Sales', 'Marketing', 'Support', 'Engineering', 'Design'];

const groupedColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))'];
const stackedColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))'];

// Simple bar data
const simpleBarData = [
    { month: 0, value: 12000 },
    { month: 1, value: 19000 },
    { month: 2, value: 15000 },
    { month: 3, value: 22000 },
    { month: 4, value: 28000 },
    { month: 5, value: 25000 },
];

// Grouped bar data
const groupedBarData = [
    { month: 0, desktop: 186, mobile: 80 },
    { month: 1, desktop: 305, mobile: 200 },
    { month: 2, desktop: 237, mobile: 120 },
    { month: 3, desktop: 273, mobile: 190 },
    { month: 4, desktop: 209, mobile: 130 },
    { month: 5, desktop: 314, mobile: 240 },
];

// Horizontal bar data
const horizontalBarData = [
    { category: 0, value: 450 },
    { category: 1, value: 380 },
    { category: 2, value: 290 },
    { category: 3, value: 520 },
    { category: 4, value: 340 },
];

// Stacked bar data
const stackedBarData = [
    { month: 0, productA: 100, productB: 80, productC: 60 },
    { month: 1, productA: 120, productB: 100, productC: 80 },
    { month: 2, productA: 90, productB: 70, productC: 50 },
    { month: 3, productA: 150, productB: 120, productC: 90 },
    { month: 4, productA: 130, productB: 110, productC: 70 },
    { month: 5, productA: 160, productB: 140, productC: 100 },
];

// Negative values data
const negativeBarData = [
    { month: 0, profit: 15000 },
    { month: 1, profit: -8000 },
    { month: 2, profit: 22000 },
    { month: 3, profit: -5000 },
    { month: 4, profit: 30000 },
    { month: 5, profit: 18000 },
];

// Interactive data
const interactiveData = [
    { month: 0, revenue: 45000, expenses: 32000 },
    { month: 1, revenue: 52000, expenses: 38000 },
    { month: 2, revenue: 48000, expenses: 35000 },
    { month: 3, revenue: 61000, expenses: 42000 },
    { month: 4, revenue: 55000, expenses: 40000 },
    { month: 5, revenue: 67000, expenses: 45000 },
];

const simpleBarTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}: $${(d.value / 1000).toFixed(1)}k`;
};

const groupedBarTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}<br/>Desktop: ${d.desktop}<br/>Mobile: ${d.mobile}`;
};

const horizontalBarTooltip = (d: any) => {
    if (!d) return '';
    return `${categories[d.category]}: ${d.value}`;
};

const stackedBarTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}<br/>A: ${d.productA}<br/>B: ${d.productB}<br/>C: ${d.productC}`;
};

const negativeBarTooltip = (d: any) => {
    if (!d) return '';
    const sign = d.profit >= 0 ? '+' : '';
    return `${months[d.month]}: ${sign}$${(d.profit / 1000).toFixed(1)}k`;
};

const crosshairTemplate = (d: any) => {
    if (!d) return '';
    return `Revenue: $${d.revenue?.toLocaleString() || 0}<br/>Expenses: $${d.expenses?.toLocaleString() || 0}`;
};

// Mini bar data for cards
const generateMiniData = () => Array.from({ length: 12 }, (_, i) => ({ x: i, y: Math.random() * 100 + 20 }));
const miniBarData1 = generateMiniData();
const miniBarData2 = generateMiniData();
const miniBarData3 = generateMiniData();
const miniBarData4 = generateMiniData();
</script>
