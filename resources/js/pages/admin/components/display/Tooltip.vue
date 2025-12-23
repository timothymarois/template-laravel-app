<template>
    <LayoutApp
        title="Components - Tooltip & Popover"
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

            <!-- Popover Positions -->
            <Card>
                <CardHeader>
                    <CardTitle>Popover Positions</CardTitle>
                    <CardDescription>Popovers can appear on any side with alignment options</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Top</Button>
                            </PopoverTrigger>
                            <PopoverContent side="top" class="w-48">
                                <p class="text-sm">Popover positioned on top</p>
                            </PopoverContent>
                        </Popover>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Bottom</Button>
                            </PopoverTrigger>
                            <PopoverContent side="bottom" class="w-48">
                                <p class="text-sm">Popover positioned on bottom</p>
                            </PopoverContent>
                        </Popover>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Left</Button>
                            </PopoverTrigger>
                            <PopoverContent side="left" class="w-48">
                                <p class="text-sm">Popover positioned on left</p>
                            </PopoverContent>
                        </Popover>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Right</Button>
                            </PopoverTrigger>
                            <PopoverContent side="right" class="w-48">
                                <p class="text-sm">Popover positioned on right</p>
                            </PopoverContent>
                        </Popover>
                    </div>
                </CardContent>
            </Card>

            <!-- Popover Alignment -->
            <Card>
                <CardHeader>
                    <CardTitle>Popover Alignment</CardTitle>
                    <CardDescription>Align popovers to start, center, or end of the trigger</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Align Start</Button>
                            </PopoverTrigger>
                            <PopoverContent align="start" class="w-48">
                                <p class="text-sm">Aligned to the start</p>
                            </PopoverContent>
                        </Popover>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Align Center</Button>
                            </PopoverTrigger>
                            <PopoverContent align="center" class="w-48">
                                <p class="text-sm">Aligned to the center</p>
                            </PopoverContent>
                        </Popover>
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">Align End</Button>
                            </PopoverTrigger>
                            <PopoverContent align="end" class="w-48">
                                <p class="text-sm">Aligned to the end</p>
                            </PopoverContent>
                        </Popover>
                    </div>
                </CardContent>
            </Card>

            <!-- Popover with Rich Content -->
            <Card>
                <CardHeader>
                    <CardTitle>Popover Content</CardTitle>
                    <CardDescription>Popovers can contain any content including forms and actions</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">
                                    <User class="size-4 mr-2" />
                                    Profile
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-64">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                                            <User class="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">John Doe</p>
                                            <p class="text-xs text-muted-foreground">john@example.com</p>
                                        </div>
                                    </div>
                                    <div class="border-t pt-3 space-y-1">
                                        <Button variant="ghost" size="sm" class="w-full justify-start">View Profile</Button>
                                        <Button variant="ghost" size="sm" class="w-full justify-start">Settings</Button>
                                        <Button variant="ghost" size="sm" class="w-full justify-start text-destructive">Sign Out</Button>
                                    </div>
                                </div>
                            </PopoverContent>
                        </Popover>

                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">
                                    <Calendar class="size-4 mr-2" />
                                    Pick Date
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto">
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium">Select a date</h4>
                                    <Input type="date" />
                                    <div class="flex justify-end gap-2">
                                        <PopoverClose as-child>
                                            <Button variant="outline" size="sm">Cancel</Button>
                                        </PopoverClose>
                                        <Button size="sm">Apply</Button>
                                    </div>
                                </div>
                            </PopoverContent>
                        </Popover>

                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline">
                                    <Share class="size-4 mr-2" />
                                    Share
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-72">
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium">Share this page</h4>
                                    <div class="flex gap-2">
                                        <Input value="https://example.com/page" readonly class="text-xs" />
                                        <Button size="sm" variant="secondary">Copy</Button>
                                    </div>
                                    <div class="flex gap-2 pt-2">
                                        <Button variant="outline" size="sm" class="flex-1">Twitter</Button>
                                        <Button variant="outline" size="sm" class="flex-1">Facebook</Button>
                                        <Button variant="outline" size="sm" class="flex-1">Email</Button>
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
import { Badge } from '@/components/ui/badge';
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
    PopoverClose,
} from '@/components/ui/popover';
import { Calendar, HelpCircle, Settings, Share, User } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
