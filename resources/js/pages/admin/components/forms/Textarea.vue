<template>
    <LayoutApp
        title="Components - Textarea"
        pageTitle="Textarea"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Textarea -->
            <Card>
                <CardHeader>
                    <CardTitle>Textarea</CardTitle>
                    <CardDescription>Multi-line text input fields with various configurations</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Textarea placeholder="Enter your message..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Textarea v-model="textareaValue" placeholder="Enter your message..." fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Read Only</div>
                            <Textarea :model-value="readOnlyValue" readonly fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Textarea v-model="disabledValue" placeholder="Disabled" disabled fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Textarea Rows -->
            <Card>
                <CardHeader>
                    <CardTitle>Textarea Rows</CardTitle>
                    <CardDescription>Different row heights for various content needs</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">2 Rows</div>
                            <Textarea placeholder="Short input..." :rows="2" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">3 Rows (Default)</div>
                            <Textarea placeholder="Default height..." :rows="3" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">5 Rows</div>
                            <Textarea placeholder="More space..." :rows="5" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">8 Rows</div>
                            <Textarea placeholder="Lots of content..." :rows="8" fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Textarea Invalid -->
            <Card>
                <CardHeader>
                    <CardTitle>Textarea Invalid</CardTitle>
                    <CardDescription>Error states for form validation</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <Textarea placeholder="Required field" invalid fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <Textarea v-model="invalidValue" placeholder="Invalid input" invalid fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Too Short</div>
                            <Textarea v-model="tooShortValue" placeholder="Minimum 50 characters" invalid fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Textarea v-model="invalidDisabled" placeholder="Invalid disabled" invalid disabled fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Textarea Resize -->
            <Card>
                <CardHeader>
                    <CardTitle>Textarea Resize</CardTitle>
                    <CardDescription>Control how the textarea can be resized</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Vertical (Default)</div>
                            <Textarea placeholder="Resize vertically..." resize="vertical" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Horizontal</div>
                            <Textarea placeholder="Resize horizontally..." resize="horizontal" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Both</div>
                            <Textarea placeholder="Resize any direction..." resize="both" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">None</div>
                            <Textarea placeholder="Cannot resize..." resize="none" fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Textarea with Label -->
            <Card>
                <CardHeader>
                    <CardTitle>Textarea with Label</CardTitle>
                    <CardDescription>Textarea fields with labels using LabelField component</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <LabelField label="Description" name="description">
                            <Textarea v-model="descriptionValue" placeholder="Enter a description..." fluid />
                        </LabelField>
                        <LabelField label="Bio" name="bio" required>
                            <Textarea v-model="bioValue" placeholder="Tell us about yourself..." fluid />
                        </LabelField>
                        <LabelField label="Notes" name="notes" hint="Optional additional information">
                            <Textarea v-model="notesValue" placeholder="Any additional notes..." fluid />
                        </LabelField>
                        <LabelField label="Feedback" name="feedback" required :error="feedbackError">
                            <Textarea v-model="feedbackValue" placeholder="Your feedback..." invalid fluid />
                        </LabelField>
                    </div>
                </CardContent>
            </Card>

            <!-- Character Counter -->
            <Card>
                <CardHeader>
                    <CardTitle>Character Counter</CardTitle>
                    <CardDescription>Track character count with max length</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <div class="text-xs text-muted-foreground mb-1">Tweet (280 chars max)</div>
                            <Textarea
                                v-model="tweetValue"
                                placeholder="What's happening?"
                                :rows="3"
                                maxlength="280"
                                fluid
                            />
                            <div class="text-xs text-right" :class="tweetValue.length > 260 ? 'text-destructive' : 'text-muted-foreground'">
                                {{ tweetValue.length }}/280
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xs text-muted-foreground mb-1">Review (500 chars max)</div>
                            <Textarea
                                v-model="reviewValue"
                                placeholder="Write your review..."
                                :rows="3"
                                maxlength="500"
                                fluid
                            />
                            <div class="text-xs text-right" :class="reviewValue.length > 450 ? 'text-destructive' : 'text-muted-foreground'">
                                {{ reviewValue.length }}/500
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Use Cases -->
            <Card>
                <CardHeader>
                    <CardTitle>Common Use Cases</CardTitle>
                    <CardDescription>Examples of textarea in different contexts</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Comment Form -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Comment Form</h4>
                            <Textarea v-model="commentValue" placeholder="Write a comment..." :rows="3" fluid />
                            <div class="flex justify-end">
                                <Button>Post Comment</Button>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Contact Message</h4>
                            <Input placeholder="Subject" fluid />
                            <Textarea v-model="messageValue" placeholder="Your message..." :rows="4" fluid />
                            <div class="flex justify-end gap-2">
                                <Button variant="outline">Cancel</Button>
                                <Button>Send Message</Button>
                            </div>
                        </div>

                        <!-- Code Input -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Code Snippet</h4>
                            <Textarea
                                v-model="codeValue"
                                placeholder="Paste your code here..."
                                :rows="5"
                                class="font-mono text-xs"
                                resize="none"
                                fluid
                            />
                        </div>

                        <!-- JSON Input -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">JSON Data</h4>
                            <Textarea
                                v-model="jsonValue"
                                :placeholder="'{&quot;key&quot;: &quot;value&quot;}'"
                                :rows="5"
                                class="font-mono text-xs"
                                resize="none"
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { LabelField } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Basic states
const textareaValue = ref('This is some example text that spans multiple lines.\n\nYou can edit this content.');
const readOnlyValue = ref('This content is read-only and cannot be modified by the user.');
const disabledValue = ref('This textarea is disabled');

// Invalid states
const invalidValue = ref('This content has an error');
const tooShortValue = ref('Too short');
const invalidDisabled = ref('Error state');

// Label field examples
const descriptionValue = ref('');
const bioValue = ref('');
const notesValue = ref('');
const feedbackValue = ref('');
const feedbackError = 'Please provide more detailed feedback';

// Character counter
const tweetValue = ref('');
const reviewValue = ref('');

// Use cases
const commentValue = ref('');
const messageValue = ref('');
const codeValue = ref(`function hello() {
  console.log("Hello, World!");
}`);
const jsonValue = ref(`{
  "name": "John Doe",
  "email": "john@example.com",
  "active": true
}`);
</script>
