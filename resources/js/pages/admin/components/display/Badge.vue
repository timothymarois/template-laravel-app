<template>
    <LayoutApp
        title="Components - Badge"
        pageTitle="Badge"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Badge Variants -->
            <Card>
                <CardHeader>
                    <CardTitle>Badge Variants</CardTitle>
                    <CardDescription>Built-in badge style variants</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-3">
                        <Badge variant="default">Default</Badge>
                        <Badge variant="secondary">Secondary</Badge>
                        <Badge variant="destructive">Destructive</Badge>
                        <Badge variant="outline">Outline</Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Badge with Icons -->
            <Card>
                <CardHeader>
                    <CardTitle>Badges with Icons</CardTitle>
                    <CardDescription>Combine badges with icons for better context</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-3">
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
                        <Badge variant="default">
                            <Star class="size-3" />
                            Featured
                        </Badge>
                        <Badge variant="secondary">
                            <Zap class="size-3" />
                            New
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Color Badges -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Colors</CardTitle>
                    <CardDescription>Status badges with semantic colors</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-3">
                        <Badge class="bg-green-500 hover:bg-green-500/80 border-transparent text-white">
                            <Check class="size-3" />
                            Success
                        </Badge>
                        <Badge class="bg-yellow-500 hover:bg-yellow-500/80 border-transparent text-white">
                            <AlertTriangle class="size-3" />
                            Warning
                        </Badge>
                        <Badge class="bg-blue-500 hover:bg-blue-500/80 border-transparent text-white">
                            <Info class="size-3" />
                            Info
                        </Badge>
                        <Badge class="bg-purple-500 hover:bg-purple-500/80 border-transparent text-white">
                            <Sparkles class="size-3" />
                            Premium
                        </Badge>
                        <Badge class="bg-orange-500 hover:bg-orange-500/80 border-transparent text-white">
                            <Flame class="size-3" />
                            Hot
                        </Badge>
                        <Badge class="bg-gray-500 hover:bg-gray-500/80 border-transparent text-white">
                            <Ban class="size-3" />
                            Disabled
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Outline Color Badges -->
            <Card>
                <CardHeader>
                    <CardTitle>Outline Colors</CardTitle>
                    <CardDescription>Subtle colored outline badges</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-3">
                        <Badge class="border-green-500 text-green-600 bg-green-50 dark:bg-green-950 hover:bg-green-100 dark:hover:bg-green-900">
                            Active
                        </Badge>
                        <Badge class="border-yellow-500 text-yellow-600 bg-yellow-50 dark:bg-yellow-950 hover:bg-yellow-100 dark:hover:bg-yellow-900">
                            Pending
                        </Badge>
                        <Badge class="border-blue-500 text-blue-600 bg-blue-50 dark:bg-blue-950 hover:bg-blue-100 dark:hover:bg-blue-900">
                            In Progress
                        </Badge>
                        <Badge class="border-red-500 text-red-600 bg-red-50 dark:bg-red-950 hover:bg-red-100 dark:hover:bg-red-900">
                            Cancelled
                        </Badge>
                        <Badge class="border-purple-500 text-purple-600 bg-purple-50 dark:bg-purple-950 hover:bg-purple-100 dark:hover:bg-purple-900">
                            Review
                        </Badge>
                        <Badge class="border-gray-500 text-gray-600 bg-gray-50 dark:bg-gray-950 hover:bg-gray-100 dark:hover:bg-gray-900">
                            Archived
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Badge in Context -->
            <Card>
                <CardHeader>
                    <CardTitle>Badges in Context</CardTitle>
                    <CardDescription>Using badges alongside other elements</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Notifications</span>
                        <Badge variant="destructive" class="h-5 min-w-5 justify-center p-0 text-[10px]">3</Badge>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Messages</span>
                        <Badge variant="default" class="h-5 min-w-5 justify-center p-0 text-[10px]">12</Badge>
                    </div>
                    <div class="flex items-center gap-3">
                        <Button variant="outline">
                            Inbox
                            <Badge variant="secondary" class="ml-2 h-5 min-w-5 justify-center p-0 text-[10px]">99+</Badge>
                        </Button>
                        <Button variant="outline">
                            Updates
                            <Badge class="ml-2 h-5 min-w-5 justify-center p-0 text-[10px] bg-blue-500 text-white border-transparent">5</Badge>
                        </Button>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm">Version</span>
                        <Badge variant="outline">v2.0.0</Badge>
                        <Badge class="bg-green-500 hover:bg-green-500/80 border-transparent text-white text-[10px]">Latest</Badge>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Check,
    Clock,
    AlertCircle,
    Circle,
    Star,
    Zap,
    AlertTriangle,
    Info,
    Sparkles,
    Flame,
    Ban,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
