<template>
    <LayoutApp
        title="Components - Badge & Avatar"
        pageTitle="Display"
        :pageNavItems="sideNavItems"
        :pageTabs="displayTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Badge</CardTitle>
                    <CardDescription>Labels and status indicators</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Variants</h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge variant="default">Default</Badge>
                            <Badge variant="secondary">Secondary</Badge>
                            <Badge variant="destructive">Destructive</Badge>
                            <Badge variant="outline">Outline</Badge>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Icons</h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge variant="default">
                                <Check class="size-3" />
                                Completed
                            </Badge>
                            <Badge variant="secondary">
                                <Clock class="size-3" />
                                Pending
                            </Badge>
                            <Badge variant="destructive">
                                <AlertCircle class="size-3" />
                                Error
                            </Badge>
                            <Badge variant="outline">
                                <Circle class="size-3" />
                                Draft
                            </Badge>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Status Examples</h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge class="bg-green-500 hover:bg-green-500/80 border-transparent text-white">Active</Badge>
                            <Badge class="bg-yellow-500 hover:bg-yellow-500/80 border-transparent text-white">Warning</Badge>
                            <Badge class="bg-blue-500 hover:bg-blue-500/80 border-transparent text-white">Info</Badge>
                            <Badge class="bg-gray-500 hover:bg-gray-500/80 border-transparent text-white">Inactive</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Avatar</CardTitle>
                    <CardDescription>User profile images and initials</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Sizes</h4>
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <Avatar size="sm" shape="circle">
                                    <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                    <AvatarFallback>JD</AvatarFallback>
                                </Avatar>
                                <div class="text-xs text-muted-foreground mt-1">Small</div>
                            </div>
                            <div class="text-center">
                                <Avatar size="base" shape="circle">
                                    <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                    <AvatarFallback>JD</AvatarFallback>
                                </Avatar>
                                <div class="text-xs text-muted-foreground mt-1">Base</div>
                            </div>
                            <div class="text-center">
                                <Avatar size="lg" shape="circle">
                                    <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                    <AvatarFallback>JD</AvatarFallback>
                                </Avatar>
                                <div class="text-xs text-muted-foreground mt-1">Large</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Shapes</h4>
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <Avatar size="base" shape="circle">
                                    <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                    <AvatarFallback>JD</AvatarFallback>
                                </Avatar>
                                <div class="text-xs text-muted-foreground mt-1">Circle</div>
                            </div>
                            <div class="text-center">
                                <Avatar size="base" shape="square">
                                    <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                    <AvatarFallback>JD</AvatarFallback>
                                </Avatar>
                                <div class="text-xs text-muted-foreground mt-1">Square</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Fallback (No Image)</h4>
                        <div class="flex items-center gap-4">
                            <Avatar size="sm" shape="circle">
                                <AvatarFallback>AB</AvatarFallback>
                            </Avatar>
                            <Avatar size="sm" shape="circle">
                                <AvatarFallback>CD</AvatarFallback>
                            </Avatar>
                            <Avatar size="sm" shape="circle">
                                <AvatarFallback>EF</AvatarFallback>
                            </Avatar>
                            <Avatar size="sm" shape="circle">
                                <AvatarFallback>GH</AvatarFallback>
                            </Avatar>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Avatar Stack</h4>
                        <div class="flex -space-x-2">
                            <Avatar size="sm" shape="circle" class="border-2 border-background">
                                <AvatarImage src="https://github.com/shadcn.png" alt="User" />
                                <AvatarFallback>U1</AvatarFallback>
                            </Avatar>
                            <Avatar size="sm" shape="circle" class="border-2 border-background">
                                <AvatarFallback>U2</AvatarFallback>
                            </Avatar>
                            <Avatar size="sm" shape="circle" class="border-2 border-background">
                                <AvatarFallback>U3</AvatarFallback>
                            </Avatar>
                            <Avatar size="sm" shape="circle" class="border-2 border-background">
                                <AvatarFallback class="text-xs">+5</AvatarFallback>
                            </Avatar>
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
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Check, Clock, AlertCircle, Circle } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, displayTabs } = useShowcaseNav();
</script>
