<template>
    <AppLayout
        title="Components - Avatar"
        pageTitle="Avatar"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Avatar with Images -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatars with Images</CardTitle>
                    <CardDescription>Profile avatars with actual photos</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4">
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/shadcn.png" alt="Tom Cook" />
                            <AvatarFallback>TC</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/huntabyte.png" alt="Sarah Wilson" />
                            <AvatarFallback>SW</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/mdo.png" alt="John Miller" />
                            <AvatarFallback>JM</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/evanw.png" alt="Emily Davis" />
                            <AvatarFallback>ED</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/taylorotwell.png" alt="Michael Brown" />
                            <AvatarFallback>MB</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/adamwathan.png" alt="Lisa Chen" />
                            <AvatarFallback>LC</AvatarFallback>
                        </Avatar>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar Sizes -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatar Sizes</CardTitle>
                    <CardDescription>Small, base, and large avatar sizes</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-end gap-4">
                        <div class="text-center">
                            <Avatar size="sm" shape="circle">
                                <AvatarImage src="https://github.com/taylorotwell.png" alt="User" />
                                <AvatarFallback>SM</AvatarFallback>
                            </Avatar>
                            <div class="text-xs text-muted-foreground mt-2">Small</div>
                        </div>
                        <div class="text-center">
                            <Avatar size="base" shape="circle">
                                <AvatarImage src="https://github.com/taylorotwell.png" alt="User" />
                                <AvatarFallback>BS</AvatarFallback>
                            </Avatar>
                            <div class="text-xs text-muted-foreground mt-2">Base</div>
                        </div>
                        <div class="text-center">
                            <Avatar size="lg" shape="circle">
                                <AvatarImage src="https://github.com/taylorotwell.png" alt="User" />
                                <AvatarFallback>LG</AvatarFallback>
                            </Avatar>
                            <div class="text-xs text-muted-foreground mt-2">Large</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar Shapes -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatar Shapes</CardTitle>
                    <CardDescription>Circle and square avatar shapes</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <Avatar size="base" shape="circle">
                                <AvatarImage src="https://github.com/huntabyte.png" alt="User" />
                                <AvatarFallback>CL</AvatarFallback>
                            </Avatar>
                            <div class="text-xs text-muted-foreground mt-2">Circle</div>
                        </div>
                        <div class="text-center">
                            <Avatar size="base" shape="square">
                                <AvatarImage src="https://github.com/huntabyte.png" alt="User" />
                                <AvatarFallback>SQ</AvatarFallback>
                            </Avatar>
                            <div class="text-xs text-muted-foreground mt-2">Square</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar Fallback -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatar Fallback</CardTitle>
                    <CardDescription>Initials with different background colors</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4">
                        <Avatar size="sm" shape="circle">
                            <AvatarFallback class="bg-red-500 text-white">AB</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarFallback class="bg-orange-500 text-white">CD</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarFallback class="bg-yellow-500 text-white">EF</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarFallback class="bg-green-500 text-white">GH</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarFallback class="bg-blue-500 text-white">IJ</AvatarFallback>
                        </Avatar>
                        <Avatar size="sm" shape="circle">
                            <AvatarFallback class="bg-purple-500 text-white">KL</AvatarFallback>
                        </Avatar>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar with Status -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatar with Status</CardTitle>
                    <CardDescription>Online/offline status indicators</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <Avatar size="sm" shape="circle">
                                <AvatarImage src="https://github.com/sindresorhus.png" alt="Online User" />
                                <AvatarFallback>ON</AvatarFallback>
                            </Avatar>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-background"></span>
                        </div>
                        <div class="relative">
                            <Avatar size="sm" shape="circle">
                                <AvatarImage src="https://github.com/huntabyte.png" alt="Away User" />
                                <AvatarFallback>AW</AvatarFallback>
                            </Avatar>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-yellow-500 border-2 border-background"></span>
                        </div>
                        <div class="relative">
                            <Avatar size="sm" shape="circle">
                                <AvatarImage src="https://github.com/mdo.png" alt="Busy User" />
                                <AvatarFallback>BS</AvatarFallback>
                            </Avatar>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-red-500 border-2 border-background"></span>
                        </div>
                        <div class="relative">
                            <Avatar size="sm" shape="circle">
                                <AvatarFallback>OF</AvatarFallback>
                            </Avatar>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-gray-400 border-2 border-background"></span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar Stack -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatar Stack</CardTitle>
                    <CardDescription>Overlapping avatars for groups with hover tooltips</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <TooltipProvider :delay-duration="0">
                        <div class="flex -space-x-3">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Avatar size="sm" shape="circle" class="border-2 border-background relative hover:z-10 transition-transform hover:scale-110 cursor-pointer">
                                        <AvatarImage src="https://github.com/shadcn.png" alt="Tom Cook" />
                                        <AvatarFallback>TC</AvatarFallback>
                                    </Avatar>
                                </TooltipTrigger>
                                <TooltipContent>Tom Cook</TooltipContent>
                            </Tooltip>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Avatar size="sm" shape="circle" class="border-2 border-background relative hover:z-10 transition-transform hover:scale-110 cursor-pointer">
                                        <AvatarImage src="https://github.com/huntabyte.png" alt="Sarah Wilson" />
                                        <AvatarFallback>SW</AvatarFallback>
                                    </Avatar>
                                </TooltipTrigger>
                                <TooltipContent>Sarah Wilson</TooltipContent>
                            </Tooltip>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Avatar size="sm" shape="circle" class="border-2 border-background relative hover:z-10 transition-transform hover:scale-110 cursor-pointer">
                                        <AvatarImage src="https://github.com/mdo.png" alt="John Miller" />
                                        <AvatarFallback>JM</AvatarFallback>
                                    </Avatar>
                                </TooltipTrigger>
                                <TooltipContent>John Miller</TooltipContent>
                            </Tooltip>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Avatar size="sm" shape="circle" class="border-2 border-background relative hover:z-10 transition-transform hover:scale-110 cursor-pointer">
                                        <AvatarImage src="https://github.com/evanw.png" alt="Emily Davis" />
                                        <AvatarFallback>ED</AvatarFallback>
                                    </Avatar>
                                </TooltipTrigger>
                                <TooltipContent>Emily Davis</TooltipContent>
                            </Tooltip>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Avatar size="sm" shape="circle" class="border-2 border-background relative hover:z-10 transition-transform hover:scale-110 cursor-pointer">
                                        <AvatarFallback class="text-xs bg-muted">+5</AvatarFallback>
                                    </Avatar>
                                </TooltipTrigger>
                                <TooltipContent>5 more people</TooltipContent>
                            </Tooltip>
                        </div>
                    </TooltipProvider>
                    <div class="flex items-center gap-2">
                        <TooltipProvider :delay-duration="0">
                            <div class="flex -space-x-2">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Avatar size="sm" shape="circle" class="border-2 border-background h-8 w-8 relative hover:z-10 cursor-pointer">
                                            <AvatarImage src="https://github.com/taylorotwell.png" alt="Michael Brown" />
                                            <AvatarFallback>MB</AvatarFallback>
                                        </Avatar>
                                    </TooltipTrigger>
                                    <TooltipContent>Michael Brown</TooltipContent>
                                </Tooltip>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Avatar size="sm" shape="circle" class="border-2 border-background h-8 w-8 relative hover:z-10 cursor-pointer">
                                            <AvatarImage src="https://github.com/adamwathan.png" alt="Lisa Chen" />
                                            <AvatarFallback>LC</AvatarFallback>
                                        </Avatar>
                                    </TooltipTrigger>
                                    <TooltipContent>Lisa Chen</TooltipContent>
                                </Tooltip>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Avatar size="sm" shape="circle" class="border-2 border-background h-8 w-8 relative hover:z-10 cursor-pointer">
                                            <AvatarFallback class="text-[10px] bg-muted">+3</AvatarFallback>
                                        </Avatar>
                                    </TooltipTrigger>
                                    <TooltipContent>3 more people</TooltipContent>
                                </Tooltip>
                            </div>
                        </TooltipProvider>
                        <span class="text-sm text-muted-foreground">are working on this</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar with Badge -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatar with Badge</CardTitle>
                    <CardDescription>Notification badges on avatars</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <Avatar size="sm" shape="circle">
                                <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                <AvatarFallback>JD</AvatarFallback>
                            </Avatar>
                            <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-destructive text-destructive-foreground text-[10px] font-medium flex items-center justify-center border-2 border-background">3</span>
                        </div>
                        <div class="relative">
                            <Avatar size="sm" shape="circle">
                                <AvatarImage src="https://github.com/huntabyte.png" alt="User" />
                                <AvatarFallback>SW</AvatarFallback>
                            </Avatar>
                            <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-blue-500 text-white text-[10px] font-medium flex items-center justify-center border-2 border-background">9</span>
                        </div>
                        <div class="relative">
                            <Avatar size="base" shape="circle">
                                <AvatarImage src="https://github.com/mdo.png" alt="User" />
                                <AvatarFallback>JM</AvatarFallback>
                            </Avatar>
                            <span class="absolute -top-1 -right-1 h-6 w-6 rounded-full bg-green-500 text-white text-xs font-medium flex items-center justify-center border-2 border-background">
                                <Check class="h-3 w-3" />
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Avatar in Context -->
            <Card>
                <CardHeader>
                    <CardTitle>Avatars in Context</CardTitle>
                    <CardDescription>Common avatar usage patterns</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center gap-3 p-3 rounded-lg border">
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/shadcn.png" alt="Tom Cook" />
                            <AvatarFallback>TC</AvatarFallback>
                        </Avatar>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">Tom Cook</p>
                            <p class="text-xs text-muted-foreground truncate">tom@example.com</p>
                        </div>
                        <Badge variant="secondary">Admin</Badge>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg border">
                        <Avatar size="sm" shape="circle">
                            <AvatarImage src="https://github.com/huntabyte.png" alt="Sarah Wilson" />
                            <AvatarFallback>SW</AvatarFallback>
                        </Avatar>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">Sarah Wilson</p>
                            <p class="text-xs text-muted-foreground truncate">sarah@example.com</p>
                        </div>
                        <Badge variant="outline">Member</Badge>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
                        <Avatar size="sm" shape="circle" class="mt-0.5">
                            <AvatarImage src="https://github.com/mdo.png" alt="John Miller" />
                            <AvatarFallback>JM</AvatarFallback>
                        </Avatar>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium">John Miller</p>
                                <span class="text-xs text-muted-foreground">2 hours ago</span>
                            </div>
                            <p class="text-sm text-muted-foreground mt-1">This looks great! I think we should ship it.</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { Check } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
