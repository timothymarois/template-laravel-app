<template>
    <LayoutApp
        title="Components - Editor"
        pageTitle="Editor"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Editor -->
            <Card>
                <CardHeader>
                    <CardTitle>Rich Text Editor</CardTitle>
                    <CardDescription>Full-featured editor with formatting toolbar powered by Tiptap</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="border rounded-lg overflow-hidden">
                        <Editor
                            v-model="basicContent"
                            placeholder="Start writing your content..."
                        />
                    </div>
                    <div class="mt-3 text-xs text-muted-foreground">
                        Output HTML: <code class="bg-muted px-1 py-0.5 rounded">{{ basicContent || '(empty)' }}</code>
                    </div>
                </CardContent>
            </Card>

            <!-- Editor States -->
            <Card>
                <CardHeader>
                    <CardTitle>Editor States</CardTitle>
                    <CardDescription>Different initial states and configurations</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty with Placeholder</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="emptyContent"
                                    placeholder="Type something here..."
                                />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Initial Content</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor v-model="prefilledContent" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Custom Placeholder</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="customPlaceholder"
                                    placeholder="Write your blog post title and content here..."
                                />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Autofocus</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="autofocusContent"
                                    placeholder="This editor autofocuses on mount"
                                    autofocus
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Toolbar Configurations -->
            <Card>
                <CardHeader>
                    <CardTitle>Toolbar Configurations</CardTitle>
                    <CardDescription>Customize which tools appear in the toolbar</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">All Tools (Default)</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="allToolsContent"
                                    placeholder="All toolbar options available"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Text Formatting Only</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="textOnlyTools"
                                    placeholder="Bold, italic, strikethrough only"
                                    :toolbarOptions="['bold', 'italic', 'strike']"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Lists and Links</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="listsLinksContent"
                                    placeholder="Lists and links only"
                                    :toolbarOptions="['bullet', 'ordered', 'link']"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">No Toolbar</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="noToolbarContent"
                                    placeholder="Editor without toolbar"
                                    :toolbar="false"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Text Only Mode -->
            <Card>
                <CardHeader>
                    <CardTitle>Text Only Mode</CardTitle>
                    <CardDescription>Plain text editing without rich text formatting</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Rich Text (Default)</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="richTextContent"
                                    placeholder="Supports HTML formatting..."
                                />
                            </div>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Output: <code class="bg-muted px-1 py-0.5 rounded text-[10px]">{{ richTextContent || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Text Only</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="plainTextContent"
                                    placeholder="Plain text only, no formatting..."
                                    textOnly
                                />
                            </div>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Output: <code class="bg-muted px-1 py-0.5 rounded text-[10px]">{{ plainTextContent || '(empty)' }}</code>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Extensions & Tools -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Extensions & Tools</CardTitle>
                    <CardDescription>Add custom Tiptap extensions and toolbar tools</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Underline Extension + Custom Tool</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="customExtensionContent"
                                    placeholder="Try the underline button..."
                                    :extensions="[Underline]"
                                    :toolbarOptions="['bold', 'italic', 'underline', 'strike']"
                                    :customTools="{ underline: UnderlineTool }"
                                />
                            </div>
                            <div class="mt-2 text-xs text-muted-foreground">
                                Output: <code class="bg-muted px-1 py-0.5 rounded text-[10px]">{{ customExtensionContent || '(empty)' }}</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Custom Tool via Slot</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="customSlotContent"
                                    placeholder="Custom tool added via slot..."
                                    :extensions="[Underline]"
                                    :toolbarOptions="['bold', 'italic']"
                                >
                                    <template #toolbar="{ editor }">
                                        <EditorToolButton
                                            :isActive="editor?.isActive('underline')"
                                            tooltip="Underline (slot)"
                                            @click="editor?.chain().focus().toggleUnderline().run()"
                                        >
                                            <IconUnderline class="size-5" />
                                        </EditorToolButton>
                                    </template>
                                </Editor>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-muted/50 rounded-lg">
                        <div class="text-xs font-medium mb-2">Usage Example:</div>
                        <pre class="text-xs overflow-x-auto"><code>// 1. Import extension and create custom tool
import Underline from '@tiptap/extension-underline'
import { EditorToolButton } from '@/components/ui/editor'

// 2. Create custom tool component (UnderlineTool.vue)
&lt;EditorToolButton
    :isActive="editor?.isActive('underline')"
    tooltip="Underline"
    @click="editor?.chain().focus().toggleUnderline().run()"
&gt;
    &lt;IconUnderline class="size-5" /&gt;
&lt;/EditorToolButton&gt;

// 3. Use in Editor
&lt;Editor
    :extensions="[Underline]"
    :toolbarOptions="['bold', 'italic', 'underline']"
    :customTools="{ underline: UnderlineTool }"
/&gt;</code></pre>
                    </div>
                </CardContent>
            </Card>

            <!-- Custom Styling -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Styling</CardTitle>
                    <CardDescription>Customize editor and toolbar appearance</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Custom Editor Padding</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="customPadding"
                                    placeholder="More padding..."
                                    editorClass="p-6 text-black text-sm dark:text-white"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Custom Toolbar Style</div>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="customToolbar"
                                    placeholder="Styled toolbar..."
                                    toolbarClass="border-b-2 border-primary bg-muted/30"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Use Cases -->
            <Card>
                <CardHeader>
                    <CardTitle>Common Use Cases</CardTitle>
                    <CardDescription>Examples of editor in different contexts</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Blog Post Editor -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Blog Post Editor</h4>
                            <Input v-model="blogTitle" placeholder="Post title..." fluid />
                            <div class="border rounded-lg overflow-hidden min-h-[200px]">
                                <Editor
                                    v-model="blogContent"
                                    placeholder="Write your blog post..."
                                    editorClass="p-4 text-black text-sm dark:text-white min-h-[150px]"
                                />
                            </div>
                            <div class="flex justify-end gap-2">
                                <Button variant="outline">Save Draft</Button>
                                <Button>Publish</Button>
                            </div>
                        </div>

                        <!-- Comment Box -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Comment Box</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="commentContent"
                                    placeholder="Write a comment..."
                                    :toolbarOptions="['bold', 'italic', 'link']"
                                    editorClass="p-3 text-black text-sm dark:text-white"
                                />
                            </div>
                            <div class="flex justify-end">
                                <Button>Post Comment</Button>
                            </div>
                        </div>

                        <!-- Email Composer -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Email Composer</h4>
                            <Input v-model="emailTo" placeholder="To: recipient@example.com" fluid />
                            <Input v-model="emailSubject" placeholder="Subject..." fluid />
                            <div class="border rounded-lg overflow-hidden min-h-[150px]">
                                <Editor
                                    v-model="emailBody"
                                    placeholder="Compose your email..."
                                    editorClass="p-4 text-black text-sm dark:text-white min-h-[100px]"
                                />
                            </div>
                            <div class="flex justify-end gap-2">
                                <Button variant="outline">Discard</Button>
                                <Button>Send</Button>
                            </div>
                        </div>

                        <!-- Notes Editor -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Quick Notes</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="notesContent"
                                    placeholder="Take notes..."
                                    :toolbarOptions="['bold', 'bullet', 'ordered']"
                                    editorClass="p-3 text-black text-sm dark:text-white"
                                />
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-muted-foreground">Auto-saved</span>
                                <Button variant="ghost">Clear</Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- With Label Field -->
            <Card>
                <CardHeader>
                    <CardTitle>With Form Labels</CardTitle>
                    <CardDescription>Editor integrated with LabelField component</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <LabelField label="Description" name="description">
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="descriptionContent"
                                    placeholder="Enter a description..."
                                />
                            </div>
                        </LabelField>
                        <LabelField label="Bio" name="bio" required>
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="bioContent"
                                    placeholder="Tell us about yourself..."
                                />
                            </div>
                        </LabelField>
                        <LabelField label="Notes" name="notes" hint="Optional additional information">
                            <div class="border rounded-lg overflow-hidden">
                                <Editor
                                    v-model="notesFieldContent"
                                    placeholder="Any additional notes..."
                                    :toolbarOptions="['bold', 'italic', 'bullet']"
                                />
                            </div>
                        </LabelField>
                        <LabelField label="Feedback" name="feedback" required :error="feedbackError">
                            <div class="border rounded-lg overflow-hidden border-destructive">
                                <Editor
                                    v-model="feedbackContent"
                                    placeholder="Your feedback..."
                                />
                            </div>
                        </LabelField>
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
import { Input } from '@/components/ui/input';
import { LabelField } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Editor, EditorToolButton } from '@/components/ui/editor';
import { IconUnderline } from '@tabler/icons-vue';
import Underline from '@tiptap/extension-underline';
import UnderlineTool from './_components/UnderlineTool.vue';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Basic editor
const basicContent = ref('');

// Editor states
const emptyContent = ref('');
const prefilledContent = ref('<p>This is some <strong>bold</strong> and <em>italic</em> text.</p><ul><li>First item</li><li>Second item</li></ul>');
const customPlaceholder = ref('');
const autofocusContent = ref('');

// Toolbar configurations
const allToolsContent = ref('');
const textOnlyTools = ref('');
const listsLinksContent = ref('');
const noToolbarContent = ref('');

// Text only mode
const richTextContent = ref('');
const plainTextContent = ref('');

// Custom extensions & tools
const customExtensionContent = ref('');
const customSlotContent = ref('');

// Custom styling
const customPadding = ref('');
const customToolbar = ref('');

// Use cases
const blogTitle = ref('');
const blogContent = ref('');
const commentContent = ref('');
const emailTo = ref('');
const emailSubject = ref('');
const emailBody = ref('');
const notesContent = ref('');

// With labels
const descriptionContent = ref('');
const bioContent = ref('');
const notesFieldContent = ref('');
const feedbackContent = ref('');
const feedbackError = 'Please provide more detailed feedback';
</script>
