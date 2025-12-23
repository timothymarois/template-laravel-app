<template>
    <LayoutApp
        title="Components - Resizable"
        pageTitle="Resizable"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-8">
            <!-- Basic Two Panel -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic Two Column Layout</CardTitle>
                    <CardDescription>A simple two-panel horizontal layout with resizable columns</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[200px] rounded-lg border">
                        <ResizablePanel :default-size="50">
                            <div class="flex h-full items-center justify-center p-6">
                                <span class="font-semibold">Left Panel</span>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle />
                        <ResizablePanel :default-size="50">
                            <div class="flex h-full items-center justify-center p-6">
                                <span class="font-semibold">Right Panel</span>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- With Visible Handle -->
            <Card>
                <CardHeader>
                    <CardTitle>With Drag Handle</CardTitle>
                    <CardDescription>Resizable panels with a visible grip handle for better discoverability</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[200px] rounded-lg border">
                        <ResizablePanel :default-size="30">
                            <div class="flex h-full items-center justify-center p-6">
                                <span class="font-semibold">Sidebar</span>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="70">
                            <div class="flex h-full items-center justify-center p-6">
                                <span class="font-semibold">Content Area</span>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- Three Column Layout -->
            <Card>
                <CardHeader>
                    <CardTitle>Three Column Layout</CardTitle>
                    <CardDescription>Multiple resizable panels with drag handles</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[200px] rounded-lg border">
                        <ResizablePanel :default-size="25" :min-size="15">
                            <div class="flex h-full items-center justify-center p-6 bg-muted/30">
                                <span class="font-semibold">Navigation</span>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="50" :min-size="30">
                            <div class="flex h-full items-center justify-center p-6">
                                <span class="font-semibold">Main Content</span>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="25" :min-size="15">
                            <div class="flex h-full items-center justify-center p-6 bg-muted/30">
                                <span class="font-semibold">Details</span>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- Min/Max Constraints -->
            <Card>
                <CardHeader>
                    <CardTitle>Size Constraints</CardTitle>
                    <CardDescription>Panels with minimum and maximum size limits (sidebar: 15-40%, content: 60-85%)</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[200px] rounded-lg border">
                        <ResizablePanel :default-size="25" :min-size="15" :max-size="40">
                            <div class="flex h-full items-center justify-center p-6 bg-primary/5">
                                <div class="text-center">
                                    <span class="font-semibold block">Sidebar</span>
                                    <span class="text-sm text-muted-foreground">15% - 40%</span>
                                </div>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="75" :min-size="60" :max-size="85">
                            <div class="flex h-full items-center justify-center p-6">
                                <div class="text-center">
                                    <span class="font-semibold block">Content</span>
                                    <span class="text-sm text-muted-foreground">60% - 85%</span>
                                </div>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- Collapsible Panel -->
            <Card>
                <CardHeader>
                    <CardTitle>Collapsible Panel</CardTitle>
                    <CardDescription>Panel that can be collapsed to minimum size with keyboard or by dragging</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[200px] rounded-lg border">
                        <ResizablePanel
                            :default-size="25"
                            :min-size="5"
                            :max-size="40"
                            collapsible
                            :collapsed-size="5"
                        >
                            <div class="flex h-full items-center justify-center p-6 bg-muted/30">
                                <div class="text-center">
                                    <PanelLeftClose class="size-5 mx-auto mb-2 text-muted-foreground" />
                                    <span class="font-semibold block">Collapsible</span>
                                    <span class="text-xs text-muted-foreground">Drag to collapse</span>
                                </div>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="75">
                            <div class="flex h-full items-center justify-center p-6">
                                <span class="font-semibold">Main Content</span>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- Nested Panels -->
            <Card>
                <CardHeader>
                    <CardTitle>Nested Panels</CardTitle>
                    <CardDescription>Horizontal panels with a vertically split right side</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[300px] rounded-lg border">
                        <ResizablePanel :default-size="30">
                            <div class="flex h-full items-center justify-center p-6 bg-muted/30">
                                <span class="font-semibold">Sidebar</span>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="70">
                            <ResizablePanelGroup direction="vertical">
                                <ResizablePanel :default-size="60">
                                    <div class="flex h-full items-center justify-center p-6">
                                        <span class="font-semibold">Editor</span>
                                    </div>
                                </ResizablePanel>
                                <ResizableHandle with-handle />
                                <ResizablePanel :default-size="40">
                                    <div class="flex h-full items-center justify-center p-6 bg-muted/30">
                                        <span class="font-semibold">Terminal</span>
                                    </div>
                                </ResizablePanel>
                            </ResizablePanelGroup>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- IDE-Style Layout -->
            <Card>
                <CardHeader>
                    <CardTitle>IDE-Style Layout</CardTitle>
                    <CardDescription>A complex layout mimicking an IDE with file explorer, editor, and output panels</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[400px] rounded-lg border">
                        <ResizablePanel :default-size="20" :min-size="10" :max-size="30">
                            <div class="h-full flex flex-col">
                                <div class="border-b px-4 py-2 font-medium text-sm bg-muted/50">Explorer</div>
                                <div class="flex-1 p-4 space-y-2 text-sm">
                                    <div class="flex items-center gap-2 text-muted-foreground">
                                        <Folder class="size-4" />
                                        <span>src</span>
                                    </div>
                                    <div class="flex items-center gap-2 pl-4">
                                        <FileCode class="size-4 text-blue-500" />
                                        <span>App.vue</span>
                                    </div>
                                    <div class="flex items-center gap-2 pl-4">
                                        <FileCode class="size-4 text-blue-500" />
                                        <span>main.ts</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-muted-foreground">
                                        <Folder class="size-4" />
                                        <span>components</span>
                                    </div>
                                </div>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="55" :min-size="30">
                            <ResizablePanelGroup direction="vertical">
                                <ResizablePanel :default-size="70" :min-size="30">
                                    <div class="h-full flex flex-col">
                                        <div class="border-b px-4 py-2 font-medium text-sm bg-muted/50 flex items-center gap-2">
                                            <FileCode class="size-4 text-blue-500" />
                                            App.vue
                                        </div>
                                        <div class="flex-1 p-4 font-mono text-sm text-muted-foreground">
                                            <div>&lt;template&gt;</div>
                                            <div class="pl-4">&lt;div class="app"&gt;</div>
                                            <div class="pl-8">&lt;h1&gt;Hello World&lt;/h1&gt;</div>
                                            <div class="pl-4">&lt;/div&gt;</div>
                                            <div>&lt;/template&gt;</div>
                                        </div>
                                    </div>
                                </ResizablePanel>
                                <ResizableHandle with-handle />
                                <ResizablePanel :default-size="30" :min-size="15">
                                    <div class="h-full flex flex-col">
                                        <div class="border-b px-4 py-2 font-medium text-sm bg-muted/50">Terminal</div>
                                        <div class="flex-1 p-4 font-mono text-sm bg-zinc-950 text-zinc-100">
                                            <div class="text-green-400">$ npm run dev</div>
                                            <div class="text-zinc-400">VITE v5.0.0 ready in 320 ms</div>
                                            <div class="text-zinc-400">➜ Local: http://localhost:5173/</div>
                                        </div>
                                    </div>
                                </ResizablePanel>
                            </ResizablePanelGroup>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="25" :min-size="15" :max-size="35">
                            <div class="h-full flex flex-col">
                                <div class="border-b px-4 py-2 font-medium text-sm bg-muted/50">Preview</div>
                                <div class="flex-1 flex items-center justify-center bg-white dark:bg-zinc-900">
                                    <div class="text-center">
                                        <h1 class="text-2xl font-bold">Hello World</h1>
                                        <p class="text-sm text-muted-foreground mt-2">Live preview</p>
                                    </div>
                                </div>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>

            <!-- Email Client Layout -->
            <Card>
                <CardHeader>
                    <CardTitle>Email Client Layout</CardTitle>
                    <CardDescription>Three-column layout typical of email applications</CardDescription>
                </CardHeader>
                <CardContent>
                    <ResizablePanelGroup direction="horizontal" class="min-h-[350px] rounded-lg border">
                        <ResizablePanel :default-size="20" :min-size="15" :max-size="25">
                            <div class="h-full flex flex-col">
                                <div class="border-b px-4 py-3 font-medium">Folders</div>
                                <div class="flex-1 p-2 space-y-1 text-sm">
                                    <div class="flex items-center gap-2 px-2 py-1.5 rounded bg-primary/10 text-primary">
                                        <Inbox class="size-4" />
                                        <span>Inbox</span>
                                        <Badge class="ml-auto">12</Badge>
                                    </div>
                                    <div class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-muted cursor-pointer">
                                        <Send class="size-4" />
                                        <span>Sent</span>
                                    </div>
                                    <div class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-muted cursor-pointer">
                                        <FileText class="size-4" />
                                        <span>Drafts</span>
                                    </div>
                                    <div class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-muted cursor-pointer">
                                        <Trash2 class="size-4" />
                                        <span>Trash</span>
                                    </div>
                                </div>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="35" :min-size="25">
                            <div class="h-full flex flex-col">
                                <div class="border-b px-4 py-3 font-medium">Messages</div>
                                <div class="flex-1 overflow-auto">
                                    <div class="border-b p-3 bg-primary/5 cursor-pointer">
                                        <div class="font-medium text-sm">John Doe</div>
                                        <div class="text-sm text-muted-foreground truncate">Re: Project Update</div>
                                        <div class="text-xs text-muted-foreground mt-1">2 hours ago</div>
                                    </div>
                                    <div class="border-b p-3 hover:bg-muted/50 cursor-pointer">
                                        <div class="font-medium text-sm">Jane Smith</div>
                                        <div class="text-sm text-muted-foreground truncate">Weekly Report</div>
                                        <div class="text-xs text-muted-foreground mt-1">Yesterday</div>
                                    </div>
                                    <div class="border-b p-3 hover:bg-muted/50 cursor-pointer">
                                        <div class="font-medium text-sm">Team Updates</div>
                                        <div class="text-sm text-muted-foreground truncate">Sprint Planning Notes</div>
                                        <div class="text-xs text-muted-foreground mt-1">2 days ago</div>
                                    </div>
                                </div>
                            </div>
                        </ResizablePanel>
                        <ResizableHandle with-handle />
                        <ResizablePanel :default-size="45" :min-size="30">
                            <div class="h-full flex flex-col">
                                <div class="border-b px-4 py-3">
                                    <div class="font-medium">Re: Project Update</div>
                                    <div class="text-sm text-muted-foreground">From: John Doe &lt;john@example.com&gt;</div>
                                </div>
                                <div class="flex-1 p-4 text-sm">
                                    <p>Hi team,</p>
                                    <p class="mt-4">Just wanted to follow up on the project status. We've made great progress this week and are on track for the milestone.</p>
                                    <p class="mt-4">Let me know if you have any questions.</p>
                                    <p class="mt-4">Best,<br />John</p>
                                </div>
                            </div>
                        </ResizablePanel>
                    </ResizablePanelGroup>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    ResizablePanelGroup,
    ResizablePanel,
    ResizableHandle,
} from '@/components/ui/resizable';
import {
    FileCode,
    FileText,
    Folder,
    Inbox,
    PanelLeftClose,
    Send,
    Trash2,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
