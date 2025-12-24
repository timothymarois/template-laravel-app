<template>
    <LayoutApp
        title="Components - Area Charts"
        pageTitle="Area Charts"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Simple Area Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Simple Area Chart</CardTitle>
                    <CardDescription>Basic filled area chart for visualizing trends</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="simpleAreaData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisArea
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.value"
                                :color="'hsl(var(--primary))'"
                                :opacity="0.3"
                            />
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.value"
                                :color="'hsl(var(--primary))'"
                                :lineWidth="2"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" />
                            <VisCrosshair :template="simpleAreaTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <!-- Stacked Area Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Stacked Area Chart</CardTitle>
                    <CardDescription>Multiple series stacked to show total and composition</CardDescription>
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
                        <VisXYContainer :data="stackedAreaData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                            <VisStackedBar
                                :x="(d: any) => d.month"
                                :y="[(d: any) => d.desktop, (d: any) => d.mobile, (d: any) => d.tablet]"
                                :color="stackedColors"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                            <VisAxis type="y" />
                            <VisCrosshair :template="stackedAreaTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-2 gap-4">
                <!-- Gradient Area Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Gradient Area Chart</CardTitle>
                        <CardDescription>Area with gradient fill for visual appeal</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-56 overflow-hidden">
                            <VisXYContainer :data="simpleAreaData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisArea
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.value"
                                    :color="'hsl(var(--primary))'"
                                    :opacity="0.2"
                                    curveType="natural"
                                />
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.value"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                    curveType="natural"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" />
                                <VisCrosshair :template="simpleAreaTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>

                <!-- Area Chart with Baseline -->
                <Card>
                    <CardHeader>
                        <CardTitle>Area Chart with Target</CardTitle>
                        <CardDescription>Compare actual vs target with filled areas</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-primary"></div>
                                <span class="text-sm">Actual</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-0.5 border-t-2 border-dashed border-muted-foreground"></div>
                                <span class="text-sm">Target</span>
                            </div>
                        </div>
                        <div class="h-48 overflow-hidden">
                            <VisXYContainer :data="targetData" :margin="{ top: 10, right: 10, bottom: 30, left: 50 }">
                                <VisArea
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.actual"
                                    :color="'hsl(var(--primary))'"
                                    :opacity="0.3"
                                />
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.actual"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                />
                                <VisLine
                                    :x="(d: any) => d.month"
                                    :y="(d: any) => d.target"
                                    :color="'hsl(var(--muted-foreground))'"
                                    :lineWidth="2"
                                    :lineDashArray="[5, 5]"
                                />
                                <VisAxis type="x" :tickFormat="(i: number) => months[i]" />
                                <VisAxis type="y" />
                                <VisCrosshair :template="targetAreaTooltip" />
                                <VisTooltip />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Interactive Area Chart -->
            <Card>
                <CardHeader>
                    <CardTitle>Interactive Area Chart</CardTitle>
                    <CardDescription>Hover to see detailed values with crosshair</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span class="text-sm">Revenue</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background: hsl(var(--chart-2))"></div>
                            <span class="text-sm">Cost</span>
                        </div>
                    </div>
                    <div class="h-72 overflow-hidden">
                        <VisXYContainer :data="interactiveData" :margin="{ top: 10, right: 10, bottom: 30, left: 60 }">
                            <VisArea
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.revenue"
                                :color="'hsl(var(--primary))'"
                                :opacity="0.2"
                            />
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.revenue"
                                :color="'hsl(var(--primary))'"
                                :lineWidth="2"
                            />
                            <VisArea
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.cost"
                                :color="'hsl(var(--chart-2))'"
                                :opacity="0.2"
                            />
                            <VisLine
                                :x="(d: any) => d.month"
                                :y="(d: any) => d.cost"
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

            <!-- Area Chart Cards -->
            <div class="grid grid-cols-3 gap-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Users</CardDescription>
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-2xl">24,531</CardTitle>
                            <span class="text-sm text-green-500">+12%</span>
                        </div>
                    </CardHeader>
                    <CardContent class="pb-4">
                        <div class="h-20 overflow-hidden">
                            <VisXYContainer :data="miniAreaData1" :margin="{ top: 5, right: 0, bottom: 0, left: 0 }">
                                <VisArea
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--primary))'"
                                    :opacity="0.2"
                                />
                                <VisLine
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--primary))'"
                                    :lineWidth="2"
                                />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Sessions</CardDescription>
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-2xl">89,234</CardTitle>
                            <span class="text-sm text-green-500">+8%</span>
                        </div>
                    </CardHeader>
                    <CardContent class="pb-4">
                        <div class="h-20 overflow-hidden">
                            <VisXYContainer :data="miniAreaData2" :margin="{ top: 5, right: 0, bottom: 0, left: 0 }">
                                <VisArea
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-2))'"
                                    :opacity="0.2"
                                />
                                <VisLine
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-2))'"
                                    :lineWidth="2"
                                />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Avg. Session</CardDescription>
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-2xl">4m 32s</CardTitle>
                            <span class="text-sm text-red-500">-3%</span>
                        </div>
                    </CardHeader>
                    <CardContent class="pb-4">
                        <div class="h-20 overflow-hidden">
                            <VisXYContainer :data="miniAreaData3" :margin="{ top: 5, right: 0, bottom: 0, left: 0 }">
                                <VisArea
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-3))'"
                                    :opacity="0.2"
                                />
                                <VisLine
                                    :x="(d: any) => d.x"
                                    :y="(d: any) => d.y"
                                    :color="'hsl(var(--chart-3))'"
                                    :lineWidth="2"
                                />
                            </VisXYContainer>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Range Area -->
            <Card>
                <CardHeader>
                    <CardTitle>Temperature Range</CardTitle>
                    <CardDescription>Show min/max ranges over time</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-primary/30"></div>
                            <span class="text-sm">Temperature Range</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span class="text-sm">Average</span>
                        </div>
                    </div>
                    <div class="h-64 overflow-hidden">
                        <VisXYContainer :data="rangeData" :margin="{ top: 10, right: 10, bottom: 30, left: 40 }">
                            <VisArea
                                :x="(d: any) => d.day"
                                :y="(d: any) => d.high"
                                :y0="(d: any) => d.low"
                                :color="'hsl(var(--primary))'"
                                :opacity="0.2"
                            />
                            <VisLine
                                :x="(d: any) => d.day"
                                :y="(d: any) => d.avg"
                                :color="'hsl(var(--primary))'"
                                :lineWidth="2"
                            />
                            <VisAxis type="x" :tickFormat="(i: number) => days[i]" />
                            <VisAxis type="y" :tickFormat="(v: number) => `${v}°F`" />
                            <VisCrosshair :template="rangeAreaTooltip" />
                            <VisTooltip />
                        </VisXYContainer>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { VisXYContainer, VisArea, VisLine, VisStackedBar, VisAxis, VisCrosshair, VisTooltip } from '@unovis/vue';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const stackedColors = ['hsl(var(--primary))', 'hsl(var(--chart-2))', 'hsl(var(--chart-3))'];

// Simple area data
const simpleAreaData = [
    { month: 0, value: 100 },
    { month: 1, value: 150 },
    { month: 2, value: 120 },
    { month: 3, value: 180 },
    { month: 4, value: 200 },
    { month: 5, value: 250 },
];

// Stacked area data
const stackedAreaData = [
    { month: 0, desktop: 186, mobile: 80, tablet: 45 },
    { month: 1, desktop: 305, mobile: 200, tablet: 78 },
    { month: 2, desktop: 237, mobile: 120, tablet: 89 },
    { month: 3, desktop: 273, mobile: 190, tablet: 120 },
    { month: 4, desktop: 209, mobile: 130, tablet: 95 },
    { month: 5, desktop: 314, mobile: 240, tablet: 145 },
];

// Target data
const targetData = [
    { month: 0, actual: 100, target: 120 },
    { month: 1, actual: 140, target: 120 },
    { month: 2, actual: 115, target: 120 },
    { month: 3, actual: 160, target: 120 },
    { month: 4, actual: 135, target: 120 },
    { month: 5, actual: 180, target: 120 },
];

// Interactive data
const interactiveData = [
    { month: 0, revenue: 45000, cost: 32000 },
    { month: 1, revenue: 52000, cost: 38000 },
    { month: 2, revenue: 48000, cost: 35000 },
    { month: 3, revenue: 61000, cost: 42000 },
    { month: 4, revenue: 55000, cost: 40000 },
    { month: 5, revenue: 67000, cost: 45000 },
];

// Range data (temperature)
const rangeData = [
    { day: 0, low: 55, high: 72, avg: 64 },
    { day: 1, low: 58, high: 75, avg: 67 },
    { day: 2, low: 52, high: 68, avg: 60 },
    { day: 3, low: 60, high: 78, avg: 69 },
    { day: 4, low: 62, high: 82, avg: 72 },
    { day: 5, low: 65, high: 85, avg: 75 },
    { day: 6, low: 58, high: 76, avg: 67 },
];

const simpleAreaTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}: ${d.value}`;
};

const stackedAreaTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}<br/>Desktop: ${d.desktop}<br/>Mobile: ${d.mobile}<br/>Tablet: ${d.tablet}`;
};

const targetAreaTooltip = (d: any) => {
    if (!d) return '';
    return `${months[d.month]}<br/>Actual: ${d.actual}<br/>Target: ${d.target}`;
};

const crosshairTemplate = (d: any) => {
    if (!d) return '';
    return `Revenue: $${d.revenue?.toLocaleString() || 0}<br/>Cost: $${d.cost?.toLocaleString() || 0}`;
};

const rangeAreaTooltip = (d: any) => {
    if (!d) return '';
    return `${days[d.day]}<br/>High: ${d.high}°F<br/>Low: ${d.low}°F<br/>Avg: ${d.avg}°F`;
};

// Mini area data
const generateMiniArea = () => {
    let value = 50;
    return Array.from({ length: 15 }, (_, i) => {
        value += (Math.random() - 0.4) * 15;
        return { x: i, y: Math.max(10, value) };
    });
};
const miniAreaData1 = generateMiniArea();
const miniAreaData2 = generateMiniArea();
const miniAreaData3 = generateMiniArea();
</script>
