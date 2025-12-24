<template>
    <LayoutApp
        title="Components - Slider"
        pageTitle="Slider"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Slider -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic Slider</CardTitle>
                    <CardDescription>Range slider for selecting numeric values</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Default</span>
                                    <span class="text-sm font-medium">{{ sliderDefault[0] }}</span>
                                </div>
                                <Slider v-model="sliderDefault" :max="100" :step="1" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">With Steps (10)</span>
                                    <span class="text-sm font-medium">{{ sliderSteps[0] }}</span>
                                </div>
                                <Slider v-model="sliderSteps" :max="100" :step="10" />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Min/Max (20-80)</span>
                                    <span class="text-sm font-medium">{{ sliderMinMax[0] }}</span>
                                </div>
                                <Slider v-model="sliderMinMax" :min="20" :max="80" :step="1" />
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-muted-foreground">Disabled</span>
                                    <span class="text-sm font-medium">{{ sliderDisabled[0] }}</span>
                                </div>
                                <Slider v-model="sliderDisabled" :max="100" :step="1" disabled />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Range Selection -->
            <Card>
                <CardHeader>
                    <CardTitle>Range Selection</CardTitle>
                    <CardDescription>Dual-thumb slider for selecting a range of values</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-muted-foreground">Range Selection</span>
                                <span class="text-sm font-medium">{{ sliderRange[0] }} - {{ sliderRange[1] }}</span>
                            </div>
                            <Slider v-model="sliderRange" :max="100" :step="1" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-muted-foreground">Price Range</span>
                                <span class="text-sm font-medium">${{ sliderPrice[0] }} - ${{ sliderPrice[1] }}</span>
                            </div>
                            <Slider v-model="sliderPrice" :max="1000" :step="10" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Real-world Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Real-world Examples</CardTitle>
                    <CardDescription>Common slider use cases</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-muted-foreground">Volume</span>
                                <span class="text-sm font-medium">{{ sliderVolume[0] }}%</span>
                            </div>
                            <Slider v-model="sliderVolume" :max="100" :step="1" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-muted-foreground">Rating (0.5 steps)</span>
                                <span class="text-sm font-medium">{{ sliderRating[0] }} stars</span>
                            </div>
                            <Slider v-model="sliderRating" :min="1" :max="5" :step="0.5" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-muted-foreground">Brightness</span>
                                <span class="text-sm font-medium">{{ sliderBrightness[0] }}%</span>
                            </div>
                            <Slider v-model="sliderBrightness" :max="100" :step="5" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-muted-foreground">Font Size</span>
                                <span class="text-sm font-medium">{{ sliderFontSize[0] }}px</span>
                            </div>
                            <Slider v-model="sliderFontSize" :min="12" :max="32" :step="1" />
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
import { Slider } from '@/components/ui/slider';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Slider examples
const sliderDefault = ref([50]);
const sliderSteps = ref([30]);
const sliderMinMax = ref([50]);
const sliderDisabled = ref([40]);
const sliderRange = ref([25, 75]);
const sliderVolume = ref([70]);
const sliderPrice = ref([100, 500]);
const sliderRating = ref([3.5]);
const sliderBrightness = ref([75]);
const sliderFontSize = ref([16]);
</script>
