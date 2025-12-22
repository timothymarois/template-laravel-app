<template>
    <LayoutApp
        title="Components - Tooltip & Popover"
        pageTitle="Display"
        :pageNavItems="sideNavItems"
        :pageTabs="displayTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Tooltip</CardTitle>
                    <CardDescription>Contextual information on hover</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Tooltip</h4>
                        <div class="flex gap-4">
                            <TooltipProvider :delay-duration="0">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button variant="outline">Hover me</Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>This is a tooltip</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Tooltip Positions</h4>
                        <div class="flex gap-4">
                            <TooltipProvider :delay-duration="0">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button variant="outline" size="sm">Top</Button>
                                    </TooltipTrigger>
                                    <TooltipContent side="top">
                                        <p>Top tooltip</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <TooltipProvider :delay-duration="0">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button variant="outline" size="sm">Bottom</Button>
                                    </TooltipTrigger>
                                    <TooltipContent side="bottom">
                                        <p>Bottom tooltip</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <TooltipProvider :delay-duration="0">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button variant="outline" size="sm">Left</Button>
                                    </TooltipTrigger>
                                    <TooltipContent side="left">
                                        <p>Left tooltip</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <TooltipProvider :delay-duration="0">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button variant="outline" size="sm">Right</Button>
                                    </TooltipTrigger>
                                    <TooltipContent side="right">
                                        <p>Right tooltip</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Tooltip Icon Helper</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-sm">Important setting</span>
                            <TooltipIcon>This setting affects how data is processed</TooltipIcon>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Popover</CardTitle>
                    <CardDescription>Rich content overlays on click</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Popover</h4>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Open Popover</Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-80">
                                <div class="grid gap-4">
                                    <div class="space-y-2">
                                        <h4 class="font-medium leading-none">Popover Title</h4>
                                        <p class="text-sm text-muted-foreground">
                                            This is a popover with more detailed content that can include any elements.
                                        </p>
                                    </div>
                                </div>
                            </PopoverContent>
                        </Popover>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Popover with Form</h4>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">
                                    <CalendarIcon class="size-4 mr-2" />
                                    Pick a date
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto p-4">
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <h4 class="font-medium leading-none">Select Date</h4>
                                        <p class="text-sm text-muted-foreground">
                                            Choose a date for your event.
                                        </p>
                                    </div>
                                    <Input type="date" />
                                    <div class="flex justify-end gap-2">
                                        <Button size="sm">Apply</Button>
                                    </div>
                                </div>
                            </PopoverContent>
                        </Popover>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/form';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
    TooltipIcon,
} from '@/components/ui/tooltip';
import {
    PopoverBase as Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, displayTabs } = useShowcaseNav();
</script>
