<template>
    <LayoutApp
        title="Components - Charts"
        pageTitle="Charts"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>Charts Overview</CardTitle>
                    <CardDescription>Beautiful, responsive charts built with Unovis for Vue</CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground mb-6">
                        Our chart components are built on top of <code class="bg-muted px-1 py-0.5 rounded">@unovis/vue</code>,
                        a modular data visualization framework. These charts support theming, responsive layouts, and interactive tooltips.
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <Link href="/admin/components/charts/area" class="block p-4 border rounded-lg hover:border-primary hover:bg-muted/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <AreaChart class="size-8 text-primary" />
                                <div>
                                    <h3 class="font-medium">Area Charts</h3>
                                    <p class="text-sm text-muted-foreground">Filled area and stacked charts</p>
                                </div>
                            </div>
                        </Link>
                        <Link href="/admin/components/charts/bar" class="block p-4 border rounded-lg hover:border-primary hover:bg-muted/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <BarChart3 class="size-8 text-primary" />
                                <div>
                                    <h3 class="font-medium">Bar Charts</h3>
                                    <p class="text-sm text-muted-foreground">Vertical and horizontal bar charts</p>
                                </div>
                            </div>
                        </Link>
                        <Link href="/admin/components/charts/line" class="block p-4 border rounded-lg hover:border-primary hover:bg-muted/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <LineChart class="size-8 text-primary" />
                                <div>
                                    <h3 class="font-medium">Line Charts</h3>
                                    <p class="text-sm text-muted-foreground">Trend lines and time series</p>
                                </div>
                            </div>
                        </Link>
                        <Link href="/admin/components/charts/pie" class="block p-4 border rounded-lg hover:border-primary hover:bg-muted/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <PieChart class="size-8 text-primary" />
                                <div>
                                    <h3 class="font-medium">Pie & Donut Charts</h3>
                                    <p class="text-sm text-muted-foreground">Circular charts for proportions</p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { AreaChart, BarChart3, LineChart, PieChart } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
