<template>
    <LayoutApp
        title="Components - Tags Input"
        pageTitle="Tags Input"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Tags Input -->
            <Card>
                <CardHeader>
                    <CardTitle>Tags Input</CardTitle>
                    <CardDescription>Type and press Enter to add tags</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <TagsInput v-model="emptyTags">
                                <TagsInputItem v-for="tag in emptyTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add tag..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <TagsInput v-model="withValueTags">
                                <TagsInputItem v-for="tag in withValueTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add more..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Many Tags</div>
                            <TagsInput v-model="manyTags">
                                <TagsInputItem v-for="tag in manyTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add..." />
                            </TagsInput>
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <TagsInput v-model="disabledTags" disabled>
                                <TagsInputItem v-for="tag in disabledTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add tag..." />
                            </TagsInput>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Tags Input Invalid -->
            <Card>
                <CardHeader>
                    <CardTitle>Tags Input Invalid</CardTitle>
                    <CardDescription>Error states for form validation</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <TagsInput v-model="invalidEmpty" invalid>
                                <TagsInputItem v-for="tag in invalidEmpty" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Required..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <TagsInput v-model="invalidWithValue" invalid>
                                <TagsInputItem v-for="tag in invalidWithValue" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Fix error..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Many Tags</div>
                            <TagsInput v-model="invalidMany" invalid>
                                <TagsInputItem v-for="tag in invalidMany" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Too many..." />
                            </TagsInput>
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <TagsInput v-model="invalidDisabled" invalid disabled>
                                <TagsInputItem v-for="tag in invalidDisabled" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Invalid..." />
                            </TagsInput>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Variants -->
            <Card>
                <CardHeader>
                    <CardTitle>Variants</CardTitle>
                    <CardDescription>Different tag styles to match your design</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Default</div>
                            <TagsInput v-model="variantDefault" variant="default">
                                <TagsInputItem v-for="tag in variantDefault" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add tag..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Secondary</div>
                            <TagsInput v-model="variantSecondary" variant="secondary">
                                <TagsInputItem v-for="tag in variantSecondary" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add tag..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Outline</div>
                            <TagsInput v-model="variantOutline" variant="outline">
                                <TagsInputItem v-for="tag in variantOutline" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add tag..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Primary</div>
                            <TagsInput v-model="variantPrimary" variant="primary">
                                <TagsInputItem v-for="tag in variantPrimary" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add tag..." />
                            </TagsInput>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Features -->
            <Card>
                <CardHeader>
                    <CardTitle>Features</CardTitle>
                    <CardDescription>Delimiters, max limit, and duplicate prevention</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Comma Delimiter</div>
                            <TagsInput v-model="delimiterTags" :add-on-paste="true" delimiter=",">
                                <TagsInputItem v-for="tag in delimiterTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Paste CSV..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Max 3 Tags</div>
                            <TagsInput v-model="maxTags" :max="3">
                                <TagsInputItem v-for="tag in maxTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput v-if="maxTags.length < 3" placeholder="Add..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">No Duplicates</div>
                            <TagsInput v-model="noDuplicates" :duplicate="false">
                                <TagsInputItem v-for="tag in noDuplicates" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Try 'Vue'..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Add on Blur</div>
                            <TagsInput v-model="blurTags" add-on-blur>
                                <TagsInputItem v-for="tag in blurTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Type & blur..." />
                            </TagsInput>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Comparison -->
            <Card>
                <CardHeader>
                    <CardTitle>Tags Input vs Multi-Select</CardTitle>
                    <CardDescription>Both handle multiple values differently</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Tags Input (Free-form)</div>
                            <TagsInput v-model="comparisonTags">
                                <TagsInputItem v-for="tag in comparisonTags" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Type anything..." />
                            </TagsInput>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Multi-Select (Predefined)</div>
                            <Select
                                v-model="comparisonSelect"
                                :options="frameworkOptions"
                                multiple
                                fluid
                                placeholder="Select frameworks..."
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form Integration -->
            <Card>
                <CardHeader>
                    <CardTitle>Form Integration</CardTitle>
                    <CardDescription>Tags input within a form context</CardDescription>
                </CardHeader>
                <CardContent>
                    <form class="space-y-4" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Project Name</div>
                                <Input v-model="formData.name" placeholder="My awesome project" fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Technologies</div>
                                <TagsInput v-model="formData.technologies">
                                    <TagsInputItem v-for="tag in formData.technologies" :key="tag" :value="tag">
                                        <TagsInputItemText />
                                        <TagsInputItemDelete />
                                    </TagsInputItem>
                                    <TagsInputInput placeholder="Add technology..." />
                                </TagsInput>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Keywords</div>
                            <TagsInput v-model="formData.keywords">
                                <TagsInputItem v-for="tag in formData.keywords" :key="tag" :value="tag">
                                    <TagsInputItemText />
                                    <TagsInputItemDelete />
                                </TagsInputItem>
                                <TagsInputInput placeholder="Add keyword..." />
                            </TagsInput>
                        </div>
                        <Button type="submit">Save Project</Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select-popover';
import {
    TagsInput,
    TagsInputInput,
    TagsInputItem,
    TagsInputItemDelete,
    TagsInputItemText,
} from '@/components/ui/tags-input';
import { useShowcaseNav } from '../_composables/useShowcaseNav';
import { toast } from 'vue-sonner';

const { sidebarItems } = useShowcaseNav();

// Basic examples
const emptyTags = ref<string[]>([]);
const withValueTags = ref(['Vue.js', 'TypeScript']);
const manyTags = ref(['Vue', 'React', 'Angular']);
const disabledTags = ref(['Read-only', 'Locked']);

// Invalid examples
const invalidEmpty = ref<string[]>([]);
const invalidWithValue = ref(['Invalid']);
const invalidMany = ref(['Too', 'Many', 'Tags']);
const invalidDisabled = ref(['Error']);

// Variant examples
const variantDefault = ref(['Vue', 'React']);
const variantSecondary = ref(['Vue', 'React']);
const variantOutline = ref(['Vue', 'React']);
const variantPrimary = ref(['Vue', 'React']);

// Feature examples
const delimiterTags = ref(['one@example.com']);
const maxTags = ref(['First', 'Second']);
const noDuplicates = ref(['Vue', 'React']);
const blurTags = ref(['Blur']);

// Comparison example
const comparisonTags = ref(['Custom Tag']);
const comparisonSelect = ref(['vue', 'react']);

const frameworkOptions = [
    { label: 'Vue.js', value: 'vue' },
    { label: 'React', value: 'react' },
    { label: 'Angular', value: 'angular' },
    { label: 'Svelte', value: 'svelte' },
    { label: 'Solid', value: 'solid' },
];

// Form example
const formData = reactive({
    name: '',
    technologies: ['Vue.js', 'Laravel'],
    keywords: [] as string[],
});

const handleSubmit = () => {
    toast.success('Form submitted!', {
        description: `Technologies: ${formData.technologies.join(', ')}`,
    });
};
</script>
