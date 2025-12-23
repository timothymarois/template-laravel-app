<template>
    <LayoutApp
        title="Components - Upload"
        pageTitle="Upload"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic File Input -->
            <Card>
                <CardHeader>
                    <CardTitle>File Input</CardTitle>
                    <CardDescription>Basic file input button for selecting files</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Empty</div>
                            <FileInput v-model="singleFile" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With File</div>
                            <FileInput v-model="preselectedFile" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <FileInput v-model="invalidFile" invalid fluid />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <FileInput v-model="disabledFile" disabled fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- File Types -->
            <Card>
                <CardHeader>
                    <CardTitle>Accepted File Types</CardTitle>
                    <CardDescription>Restrict file selection to specific types</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Images Only</div>
                            <FileInput
                                v-model="imageFile"
                                accept="image/*"
                                placeholder="Choose image..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">PDF Only</div>
                            <FileInput
                                v-model="pdfFile"
                                accept=".pdf"
                                placeholder="Choose PDF..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Documents</div>
                            <FileInput
                                v-model="docFile"
                                accept=".pdf,.doc,.docx,.txt"
                                placeholder="Choose document..."
                                fluid
                            />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">CSV/Excel</div>
                            <FileInput
                                v-model="spreadsheetFile"
                                accept=".csv,.xlsx,.xls"
                                placeholder="Choose spreadsheet..."
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Multiple Files -->
            <Card>
                <CardHeader>
                    <CardTitle>Multiple File Selection</CardTitle>
                    <CardDescription>Allow selecting multiple files at once</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Single File (Default)</div>
                            <FileInput v-model="singleOnly" fluid />
                            <div v-if="singleOnly" class="mt-2 text-xs text-muted-foreground">
                                Selected: {{ (singleOnly as File).name }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Multiple Files</div>
                            <FileInput v-model="multipleFiles" multiple fluid />
                            <div v-if="multipleFiles && (multipleFiles as File[]).length > 0" class="mt-2 text-xs text-muted-foreground">
                                Selected: {{ (multipleFiles as File[]).length }} file(s)
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Input Group Style -->
            <Card>
                <CardHeader>
                    <CardTitle>Input Group Style</CardTitle>
                    <CardDescription>File input with text display and browse button</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Basic</div>
                            <div class="flex">
                                <input
                                    type="text"
                                    readonly
                                    :value="inputGroupFile ? inputGroupFile.name : ''"
                                    placeholder="No file selected"
                                    class="flex-1 h-9 rounded-l-md border border-r-0 border-input bg-background px-3 text-sm placeholder:text-muted-foreground focus:outline-none"
                                />
                                <label class="inline-flex items-center gap-2 h-9 px-4 rounded-r-md border border-input bg-muted text-sm font-medium cursor-pointer hover:bg-accent transition-colors">
                                    <Upload class="size-4" />
                                    Browse
                                    <input
                                        type="file"
                                        class="hidden"
                                        @change="(e) => inputGroupFile = (e.target as HTMLInputElement).files?.[0] || null"
                                    />
                                </label>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Upload Button</div>
                            <div class="flex">
                                <input
                                    type="text"
                                    readonly
                                    :value="inputGroupFile2 ? inputGroupFile2.name : ''"
                                    placeholder="No file selected"
                                    class="flex-1 h-9 rounded-l-md border border-r-0 border-input bg-background px-3 text-sm placeholder:text-muted-foreground focus:outline-none"
                                />
                                <label class="inline-flex items-center gap-2 h-9 px-3 border-y border-input bg-muted text-sm cursor-pointer hover:bg-accent transition-colors">
                                    Browse
                                    <input
                                        type="file"
                                        class="hidden"
                                        @change="(e) => inputGroupFile2 = (e.target as HTMLInputElement).files?.[0] || null"
                                    />
                                </label>
                                <Button class="rounded-l-none" size="default" :disabled="!inputGroupFile2">
                                    <Upload class="size-4 mr-2" />
                                    Upload
                                </Button>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Primary Browse Button</div>
                            <div class="flex">
                                <input
                                    type="text"
                                    readonly
                                    :value="inputGroupFile3 ? inputGroupFile3.name : ''"
                                    placeholder="Select a file..."
                                    class="flex-1 h-9 rounded-l-md border border-r-0 border-input bg-background px-3 text-sm placeholder:text-muted-foreground focus:outline-none"
                                />
                                <label class="inline-flex items-center gap-2 h-9 px-4 rounded-r-md bg-primary text-primary-foreground text-sm font-medium cursor-pointer hover:bg-primary/90 transition-colors">
                                    <Upload class="size-4" />
                                    Choose File
                                    <input
                                        type="file"
                                        class="hidden"
                                        @change="(e) => inputGroupFile3 = (e.target as HTMLInputElement).files?.[0] || null"
                                    />
                                </label>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Clear Button</div>
                            <div class="flex">
                                <input
                                    type="text"
                                    readonly
                                    :value="inputGroupFile4 ? inputGroupFile4.name : ''"
                                    placeholder="No file selected"
                                    class="flex-1 h-9 rounded-l-md border border-r-0 border-input bg-background px-3 text-sm placeholder:text-muted-foreground focus:outline-none"
                                />
                                <button
                                    v-if="inputGroupFile4"
                                    type="button"
                                    class="inline-flex items-center h-9 px-2 border-y border-input bg-background text-muted-foreground hover:text-foreground cursor-pointer"
                                    @click="inputGroupFile4 = null"
                                >
                                    <X class="size-4" />
                                </button>
                                <label class="inline-flex items-center gap-2 h-9 px-4 rounded-r-md border border-input bg-muted text-sm font-medium cursor-pointer hover:bg-accent transition-colors">
                                    Browse
                                    <input
                                        type="file"
                                        class="hidden"
                                        @change="(e) => inputGroupFile4 = (e.target as HTMLInputElement).files?.[0] || null"
                                    />
                                </label>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Dropzone Basic -->
            <Card>
                <CardHeader>
                    <CardTitle>Dropzone</CardTitle>
                    <CardDescription>Drag and drop file upload area</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Single File</div>
                            <Dropzone v-model="dropzoneSingle" hint="PNG, JPG up to 10MB" />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Multiple Files</div>
                            <Dropzone
                                v-model="dropzoneMultiple"
                                multiple
                                hint="Upload up to 5 files"
                                :maxFiles="5"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Dropzone States -->
            <Card>
                <CardHeader>
                    <CardTitle>Dropzone States</CardTitle>
                    <CardDescription>Different states for validation and feedback</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid State</div>
                            <Dropzone
                                v-model="dropzoneInvalid"
                                invalid
                                hint="Please upload a valid file"
                            />
                        </div>
                        <div class="opacity-50">
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <Dropzone
                                v-model="dropzoneDisabled"
                                disabled
                                hint="Uploads are disabled"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Dropzone with Validation -->
            <Card>
                <CardHeader>
                    <CardTitle>Dropzone with Validation</CardTitle>
                    <CardDescription>File size and type restrictions with error handling</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <Dropzone
                            v-model="validatedFiles"
                            multiple
                            accept="image/*"
                            :maxSize="5 * 1024 * 1024"
                            :maxFiles="3"
                            hint="Images only, max 5MB each, up to 3 files"
                            @error="handleUploadError"
                        />
                        <div v-if="uploadError" class="text-sm text-destructive">
                            {{ uploadError }}
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- With Labels -->
            <Card>
                <CardHeader>
                    <CardTitle>With Form Labels</CardTitle>
                    <CardDescription>File inputs integrated with LabelField component</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <LabelField label="Profile Photo" name="photo">
                            <FileInput v-model="profilePhoto" accept="image/*" fluid />
                        </LabelField>
                        <LabelField label="Resume" name="resume" required>
                            <FileInput v-model="resume" accept=".pdf,.doc,.docx" fluid />
                        </LabelField>
                        <LabelField label="Attachments" name="attachments" hint="Optional supporting documents">
                            <FileInput v-model="attachments" multiple fluid />
                        </LabelField>
                        <LabelField label="ID Document" name="idDoc" required :error="idDocError">
                            <FileInput v-model="idDoc" accept="image/*,.pdf" invalid fluid />
                        </LabelField>
                    </div>
                </CardContent>
            </Card>

            <!-- Inertia Form Example -->
            <Card>
                <CardHeader>
                    <CardTitle>Inertia Form Upload</CardTitle>
                    <CardDescription>Example of file upload with Inertia.js useForm</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <LabelField label="Name" name="name">
                                <Input v-model="form.name" placeholder="Enter name..." fluid />
                            </LabelField>
                            <LabelField label="Email" name="email">
                                <Input v-model="form.email" type="email" placeholder="Enter email..." fluid />
                            </LabelField>
                        </div>
                        <LabelField label="Avatar" name="avatar">
                            <FileInput v-model="form.avatar" accept="image/*" fluid />
                        </LabelField>
                        <LabelField label="Documents" name="documents">
                            <Dropzone
                                v-model="form.documents"
                                multiple
                                accept=".pdf,.doc,.docx"
                                hint="PDF or Word documents"
                            />
                        </LabelField>
                        <div class="flex justify-end gap-2">
                            <Button variant="outline" type="button" @click="resetForm">Reset</Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <Spinner v-if="isSubmitting" class="mr-2" />
                                {{ isSubmitting ? 'Uploading...' : 'Submit' }}
                            </Button>
                        </div>
                    </form>
                    <div class="mt-4 p-3 bg-muted rounded-lg">
                        <div class="text-xs font-medium mb-2">Form Data Preview:</div>
                        <pre class="text-xs overflow-auto">{{ formPreview }}</pre>
                    </div>
                </CardContent>
            </Card>

            <!-- Use Cases -->
            <Card>
                <CardHeader>
                    <CardTitle>Common Use Cases</CardTitle>
                    <CardDescription>Practical examples of file upload patterns</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Profile Picture Upload -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Profile Picture</h4>
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded-full bg-muted flex items-center justify-center overflow-hidden">
                                    <img v-if="avatarPreview" :src="avatarPreview" class="size-full object-cover" />
                                    <User v-else class="size-8 text-muted-foreground" />
                                </div>
                                <div class="flex-1">
                                    <FileInput
                                        v-model="avatarFile"
                                        accept="image/*"
                                        placeholder="Upload photo..."
                                        fluid
                                        @change="handleAvatarChange"
                                    />
                                    <p class="mt-1 text-xs text-muted-foreground">JPG, PNG. Max 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Document Upload with Progress -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Document Upload</h4>
                            <Dropzone
                                v-model="documentFiles"
                                multiple
                                accept=".pdf,.doc,.docx"
                                :maxFiles="5"
                                hint="Drop documents here"
                            />
                            <div class="flex justify-end">
                                <Button size="sm" :disabled="!documentFiles || (documentFiles as File[]).length === 0">
                                    Upload {{ documentFiles ? (documentFiles as File[]).length : 0 }} file(s)
                                </Button>
                            </div>
                        </div>

                        <!-- Import Data -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Import Data</h4>
                            <FileInput
                                v-model="importFile"
                                accept=".csv,.xlsx"
                                placeholder="Choose CSV or Excel file..."
                                fluid
                            />
                            <div class="flex items-center justify-between">
                                <a href="#" class="text-xs text-primary hover:underline">Download template</a>
                                <Button size="sm" :disabled="!importFile">Import Data</Button>
                            </div>
                        </div>

                        <!-- Bulk Image Upload -->
                        <div class="space-y-3 p-4 border rounded-lg">
                            <h4 class="font-medium text-sm">Gallery Upload</h4>
                            <Dropzone
                                v-model="galleryFiles"
                                multiple
                                accept="image/*"
                                :maxSize="10 * 1024 * 1024"
                                hint="Drag images here. Max 10MB each."
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Code Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Usage with Inertia</CardTitle>
                    <CardDescription>How to handle file uploads with Inertia.js</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="p-4 bg-muted rounded-lg">
                            <div class="text-xs font-medium mb-2">Vue Component (Single File)</div>
                            <pre class="text-xs overflow-auto whitespace-pre-wrap"><code>import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    avatar: null as File | null,
});

const submit = () => {
    form.post('/upload', {
        forceFormData: true,
    });
};</code></pre>
                        </div>
                        <div class="p-4 bg-muted rounded-lg">
                            <div class="text-xs font-medium mb-2">Vue Component (Multiple Files)</div>
                            <pre class="text-xs overflow-auto whitespace-pre-wrap"><code>import { useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    documents: [] as File[],
});

const submit = () => {
    form.post('/upload-multiple', {
        forceFormData: true,
    });
};</code></pre>
                        </div>
                        <div class="p-4 bg-muted rounded-lg">
                            <div class="text-xs font-medium mb-2">Laravel Controller</div>
                            <pre class="text-xs overflow-auto whitespace-pre-wrap"><code>public function store(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|max:2048',
        'documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
    ]);

    // Single file
    $path = $request->file('avatar')->store('avatars');

    // Multiple files
    foreach ($request->file('documents', []) as $doc) {
        $doc->store('documents');
    }
}</code></pre>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Input, FileInput, Dropzone, LabelField } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { User, Upload, X } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Basic file input
const singleFile = ref<File | null>(null);
const preselectedFile = ref<File | null>(null);
const invalidFile = ref<File | null>(null);
const disabledFile = ref<File | null>(null);

// File types
const imageFile = ref<File | null>(null);
const pdfFile = ref<File | null>(null);
const docFile = ref<File | null>(null);
const spreadsheetFile = ref<File | null>(null);

// Single vs multiple
const singleOnly = ref<File | null>(null);
const multipleFiles = ref<File[] | null>(null);

// Input group style
const inputGroupFile = ref<File | null>(null);
const inputGroupFile2 = ref<File | null>(null);
const inputGroupFile3 = ref<File | null>(null);
const inputGroupFile4 = ref<File | null>(null);

// Dropzone
const dropzoneSingle = ref<File | null>(null);
const dropzoneMultiple = ref<File[] | null>(null);
const dropzoneInvalid = ref<File | null>(null);
const dropzoneDisabled = ref<File | null>(null);

// Validated dropzone
const validatedFiles = ref<File[] | null>(null);
const uploadError = ref<string | null>(null);

const handleUploadError = (error: { type: string; message: string }) => {
    uploadError.value = error.message;
    setTimeout(() => {
        uploadError.value = null;
    }, 5000);
};

// With labels
const profilePhoto = ref<File | null>(null);
const resume = ref<File | null>(null);
const attachments = ref<File[] | null>(null);
const idDoc = ref<File | null>(null);
const idDocError = 'Please upload a valid ID document';

// Inertia form example
const form = ref({
    name: '',
    email: '',
    avatar: null as File | null,
    documents: null as File[] | null,
});
const isSubmitting = ref(false);

const formPreview = computed(() => {
    return {
        name: form.value.name,
        email: form.value.email,
        avatar: form.value.avatar?.name || null,
        documents: form.value.documents?.map(f => f.name) || [],
    };
});

const submitForm = () => {
    isSubmitting.value = true;
    // Simulate upload
    setTimeout(() => {
        isSubmitting.value = false;
        alert('Form submitted! Check console for data.');
        console.log('Form data:', form.value);
    }, 2000);
};

const resetForm = () => {
    form.value = {
        name: '',
        email: '',
        avatar: null,
        documents: null,
    };
};

// Use cases
const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const documentFiles = ref<File[] | null>(null);
const importFile = ref<File | null>(null);
const galleryFiles = ref<File[] | null>(null);

const handleAvatarChange = (files: File[]) => {
    if (files.length > 0) {
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(files[0]);
    } else {
        avatarPreview.value = null;
    }
};
</script>
