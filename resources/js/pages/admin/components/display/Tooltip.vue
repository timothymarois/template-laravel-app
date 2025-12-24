<template>
    <LayoutApp
        title="Components - Tooltip"
        pageTitle="Tooltip"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Tooltip Positions -->
            <Card>
                <CardHeader>
                    <CardTitle>Tooltip Positions</CardTitle>
                    <CardDescription>Tooltips can appear on any side of the trigger</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Top</Button>
                                </TooltipTrigger>
                                <TooltipContent side="top">
                                    <p>Tooltip on top</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Bottom</Button>
                                </TooltipTrigger>
                                <TooltipContent side="bottom">
                                    <p>Tooltip on bottom</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Left</Button>
                                </TooltipTrigger>
                                <TooltipContent side="left">
                                    <p>Tooltip on left</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Right</Button>
                                </TooltipTrigger>
                                <TooltipContent side="right">
                                    <p>Tooltip on right</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </CardContent>
            </Card>

            <!-- Tooltip Colors -->
            <Card>
                <CardHeader>
                    <CardTitle>Tooltip Colors</CardTitle>
                    <CardDescription>Custom colored tooltips for different contexts</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Default</Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Default primary tooltip</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Destructive</Button>
                                </TooltipTrigger>
                                <TooltipContent class="bg-destructive text-destructive-foreground border-destructive-foreground/20">
                                    <p>Warning: This action is irreversible</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Secondary</Button>
                                </TooltipTrigger>
                                <TooltipContent class="bg-secondary text-secondary-foreground border-border">
                                    <p>Secondary styled tooltip</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">Light</Button>
                                </TooltipTrigger>
                                <TooltipContent class="bg-background text-foreground border-border">
                                    <p>Light background tooltip</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </CardContent>
            </Card>

            <!-- Tooltip Triggers -->
            <Card>
                <CardHeader>
                    <CardTitle>Tooltip Triggers</CardTitle>
                    <CardDescription>Different elements can trigger tooltips</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4">
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline" size="icon">
                                        <Settings class="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Settings</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="ghost" size="icon">
                                        <HelpCircle class="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Get help</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <span class="text-sm underline decoration-dotted cursor-help">Hover this text</span>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Tooltips work on any element</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Badge class="cursor-help">Badge</Badge>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Even badges can have tooltips</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </CardContent>
            </Card>

            <!-- TooltipIcon Helper -->
            <Card>
                <CardHeader>
                    <CardTitle>Tooltip Icon Helper</CardTitle>
                    <CardDescription>Pre-built info icon with tooltip for form labels and settings</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium">API Key</span>
                            <TooltipProvider :delay-duration="0">
                                <TooltipIcon>Your unique API key for authentication. Keep this secret.</TooltipIcon>
                            </TooltipProvider>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium">Rate Limit</span>
                            <TooltipProvider :delay-duration="0">
                                <TooltipIcon>Maximum number of requests allowed per minute. Contact support to increase limits.</TooltipIcon>
                            </TooltipProvider>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium">Webhook URL</span>
                            <TooltipProvider :delay-duration="0">
                                <TooltipIcon icon-class="size-4">The endpoint where we'll send event notifications.</TooltipIcon>
                            </TooltipProvider>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Tooltip with Delay -->
            <Card>
                <CardHeader>
                    <CardTitle>Tooltip Delay</CardTitle>
                    <CardDescription>Control how long before the tooltip appears</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <TooltipProvider :delay-duration="0">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">No Delay</Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Appears instantly</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="300">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">300ms Delay</Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Appears after 300ms</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider :delay-duration="700">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="outline">700ms Delay</Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Appears after 700ms</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
    TooltipIcon,
} from '@/components/ui/tooltip';
import { HelpCircle, Settings } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
