<template>
    <LayoutApp
        title="Components - Line Charts"
        pageTitle="Line Charts"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Simple Line Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Simple Line Chart</CardTitle>
                    <CardDescription>Basic line chart for time series data</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="simpleLineData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.value"
                                :color="'hsl(var(--primary))'"
                                :lineWidth="2"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" />
                            <VisCrosshair :template="simpleLineTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <!-- Multi-Line Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Multi-Line Chart</CardTitle>
                    <CardDescription>Compare multiple data series over time</CardDescription>
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
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-3))"></div>
                            <span class="text-sm">Tablet</span>
                        </div>
                    </div>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="multiLineData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.desktop"
                                :color="'hsl(var(--primary))'"
                                :lineWidth="2"
                            />
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.mobile"
                                :color="'hsl(var(--chart-2))'"
                                :lineWidth="2"
                            />
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.tablet"
                                :color="'hsl(var(--chart-3))'"
                                :lineWidth="2"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" />
                            <VisCrosshair :template="multiLineTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-2 gap-4">
                <!-- Line Chart with Points -->
                <Card>
                    <CardHeader>
                        <CardTitle>Line Chart with Points</CardTitle>
                        <CardDescription>Show data points on the line</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-64 overflow-hidden">
                            <VisXYContainer :data="simpleLineData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.value"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                />
                                <VisScatter
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.value"
                                    :color="'hsl(var(--primary))'"
                                    :size="6"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" />
                                <VisCrosshair :template="simpleLineTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>

                <!-- Curved Line Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Curved Line Chart</CardTitle>
                        <CardDescription>Smooth curved lines for better visualization</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-64 overflow-hidden">
                            <VisXYContainer :data="simpleLineData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.value"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                    curveType="natural"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" />
                                <VisCrosshair :template="simpleLineTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Line Chart with Grid -->
            <Card>
                <CardHeader>
                    <CardTitle>Line Chart with Grid</CardTitle>
                    <CardDescription>Enhanced readability with grid lines</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span class="text-sm">Revenue</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-2))"></div>
                            <span class="text-sm">Profit</span>
                        </div>
                    </div>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="revenueData" :margin="{ top: 10, right: 10, bottom: 30, left: 60 }">
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.revenue"
                                :color="'hsl(var(--primary))'"
                                :lineWidth="2"
                            />
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.profit"
                                :color="'hsl(var(--chart-2))'"
                                :lineWidth="2"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" :gridLine="true" />
                            <VisAxis type="y" :tickFormat="(v: number) => `$${v/1000}k`" :gridLine="true" />
                            <VisCrosshair :template="crosshairTemplate" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <!-- Dashed Line Chart -->
            <div class="grid grid-cols-2 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Dashed Line Chart</CardTitle>
                        <CardDescription>Use dashed lines for projections or estimates</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-0.5 bg-primary"></div>
                                <span class="text-sm">Actual</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-0.5 bg-primary border-dashed border-t-2 border-primary bg-transparent"></div>
                                <span class="text-sm">Projected</span>
                            </div>
                        </div>
                        <div class="h-56 overflow-hidden">
                            <VisXYContainer :data="projectionData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.actual"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                />
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.projected"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                    :lineDashArray="[5, 5]"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" />
                                <VisCrosshair :template="projectionTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>

                <!-- Step Line Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Step Line Chart</CardTitle>
                        <CardDescription>Discrete changes between data points</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-56 overflow-hidden">
                            <VisXYContainer :data="stepData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.price"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                    curveType="stepAfter"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" :tickFormat="(v: number) => `$${v}`" />
                                <VisCrosshair :template="stepTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sparklines -->
            <Card>
                <CardHeader>
                    <CardTitle>Sparklines</CardTitle>
                    <CardDescription>Compact inline charts for quick data visualization</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium">Page Views</p>
                                <p class="text-2xl font-bold">1,234,567</p>
                                <p class="text-xs text-green-500">+12.5%</p>
                            </div>
                            <div class="w-36 h-14 shrink-0" style="overflow: hidden;">
                                <VisXYContainer :data="sparklineData1" :margin="{ top: 4, right: 4, bottom: 4, left: 4 }" :style="{ width: '144px', height: '56px' }">
                                    <VisArea
                                        :x="(d: any) => d.x"
                                        :y="(d: any) => d.y"
                                        :color="'hsl(var(--primary))'"
                                        :opacity="0.15"
                                        curveType="natural"
                                    />
                                    <VisLine
                                        :x="(d: any) => d.x"
                                        :y="(d: any) => d.y"
                                        :color="'hsl(var(--primary))'"
                                        :lineWidth="2"
                                        curveType="natural"
                                    />
                                </VisXYContainer>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium">Bounce Rate</p>
                                <p class="text-2xl font-bold">42.3%</p>
                                <p class="text-xs text-red-500">+2.1%</p>
                            </div>
                            <div class="w-36 h-14 shrink-0" style="overflow: hidden;">
                                <VisXYContainer :data="sparklineData2" :margin="{ top: 4, right: 4, bottom: 4, left: 4 }" :style="{ width: '144px', height: '56px' }">
                                    <VisArea
                                        :x="(d: any) => d.x"
                                        :y="(d: any) => d.y"
                                        :color="'hsl(var(--destructive))'"
                                        :opacity="0.15"
                                        curveType="natural"
                                    />
                                    <VisLine
                                        :x="(d: any) => d.x"
                                        :y="(d: any) => d.y"
                                        :color="'hsl(var(--destructive))'"
                                        :lineWidth="2"
                                        curveType="natural"
                                    />
                                </VisXYContainer>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium">Session Duration</p>
                                <p class="text-2xl font-bold">3m 42s</p>
                                <p class="text-xs text-green-500">+8.3%</p>
                            </div>
                            <div class="w-36 h-14 shrink-0" style="overflow: hidden;">
                                <VisXYContainer :data="sparklineData3" :margin="{ top: 4, right: 4, bottom: 4, left: 4 }" :style="{ width: '144px', height: '56px' }">
                                    <VisArea
                                        :x="(d: any) => d.x"
                                        :y="(d: any) => d.y"
                                        :color="'hsl(var(--chart-2))'"
                                        :opacity="0.15"
                                        curveType="natural"
                                    />
                                    <VisLine
                                        :x="(d: any) => d.x"
                                        :y="(d: any) => d.y"
                                        :color="'hsl(var(--chart-2))'"
                                        :lineWidth="2"
                                        curveType="natural"
                                    />
                                </VisXYContainer>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { VisXYContainer, VisLine, VisArea, VisScatter, VisAxis, VisCrosshair, VisTooltip } from '@unovis/vue';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

// Simple line data
const simpleLineData = [
    { month: 0, value: 100 },
    { month: 1, value: 150 },
    { month: 2, value: 120 },
    { month: 3, value: 180 },
    { month: 4, value: 200 },
    { month: 5, value: 250 },
];

// Multi-line data
const multiLineData = [
    { month: 0, desktop: 186, mobile: 80, tablet: 45 },
    { month: 1, desktop: 305, mobile: 200, tablet: 78 },
    { month: 2, desktop: 237, mobile: 120, tablet: 89 },
    { month: 3, desktop: 273, mobile: 190, tablet: 120 },
    { month: 4, desktop: 209, mobile: 130, tablet: 95 },
    { month: 5, desktop: 314, mobile: 240, tablet: 145 },
];

// Revenue data
const revenueData = [
    { month: 0, revenue: 45000, profit: 12000 },
    { month: 1, revenue: 52000, profit: 15000 },
    { month: 2, revenue: 48000, profit: 14000 },
    { month: 3, revenue: 61000, profit: 19000 },
    { month: 4, revenue: 55000, profit: 17000 },
    { month: 5, revenue: 67000, profit: 22000 },
];

// Projection data
const projectionData = [
    { month: 0, actual: 100, projected: null },
    { month: 1, actual: 120, projected: null },
    { month: 2, actual: 150, projected: null },
    { month: 3, actual: 140, projected: 140 },
    { month: 4, actual: null, projected: 160 },
    { month: 5, actual: null, projected: 180 },
];

// Step data
const stepData = [
    { month: 0, price: 29 },
    { month: 1, price: 29 },
    { month: 2, price: 39 },
    { month: 3, price: 39 },
    { month: 4, price: 49 },
    { month: 5, price: 49 },
];

const simpleLineTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}: ${d.value}`;
};

const multiLineTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}<br/>Desktop: ${d.desktop}<br/>Mobile: ${d.mobile}<br/>Tablet: ${d.tablet}`;
};

const crosshairTemplate = (d: any) => {
    if (!d) return '';
    return `Revenue: $${d.revenue?.toLocaleString() || 0}<br/>Profit: $${d.profit?.toLocaleString() || 0}`;
};

const projectionTooltip = (d: any) => {
    if (!d) return '';
    const actual = d.actual !== null ? `Actual: ${d.actual}` : '';
    const projected = d.projected !== null ? `Projected: ${d.projected}` : '';
    return `${months[d.month]}<br/>${[actual, projected].filter(Boolean).join('<br/>')}`;
};

const stepTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}: $${d.price}`;
};

// Sparkline data
const generateSparkline = () => Array.from({ length: 20 }, (_, i) => ({ x: i, y: Math.random() * 50 + 25 }));
const sparklineData1 = generateSparkline();
const sparklineData2 = generateSparkline();
const sparklineData3 = generateSparkline();
</script>
