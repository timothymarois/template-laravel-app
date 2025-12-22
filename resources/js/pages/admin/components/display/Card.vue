<template>
    <LayoutApp
        title="Components - Card"
        pageTitle="Display"
        :pageNavItems="sideNavItems"
        :pageTabs="displayTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Card</CardTitle>
                    <CardDescription>Container for grouping related content</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Card</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Card Title</CardTitle>
                                    <CardDescription>Card description text</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-muted-foreground">This is the main content area of the card.</p>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>With Footer</CardTitle>
                                    <CardDescription>Card with action footer</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-muted-foreground">Card content goes here.</p>
                                </CardContent>
                                <CardFooter class="justify-end gap-2">
                                    <Button variant="outline" size="sm">Cancel</Button>
                                    <Button size="sm">Save</Button>
                                </CardFooter>
                            </Card>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Card Variations</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <Card class="hover:border-primary/50 transition-colors cursor-pointer">
                                <CardHeader>
                                    <CardTitle class="text-base">Clickable Card</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-muted-foreground">Hover to see border change</p>
                                </CardContent>
                            </Card>

                            <Card class="bg-muted/50">
                                <CardHeader>
                                    <CardTitle class="text-base">Muted Background</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-muted-foreground">With subtle background</p>
                                </CardContent>
                            </Card>

                            <Card class="border-primary">
                                <CardHeader>
                                    <CardTitle class="text-base">Highlighted</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-muted-foreground">With accent border</p>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, displayTabs } = useShowcaseNav();
</script>
