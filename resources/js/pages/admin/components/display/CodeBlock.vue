<template>
    <AppLayout
        title="Components - CodeBlock"
        pageTitle="CodeBlock"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>Auto-detect</CardTitle>
                    <CardDescription>No <code>language</code> prop — highlight.js detects from the snippet. The dropdown label shows the detected language directly (e.g. "PHP"), with "Auto-detect" still selectable in the menu.</CardDescription>
                </CardHeader>
                <CardContent>
                    <CodeBlock :code="phpSnippet" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Explicit language: PHP</CardTitle>
                    <CardDescription>Pass <code>language="php"</code> to force highlighting. The dropdown still lets the viewer change languages.</CardDescription>
                </CardHeader>
                <CardContent>
                    <CodeBlock :code="phpSnippet" language="php" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Explicit language: JavaScript</CardTitle>
                    <CardDescription>Vue and JSX-flavored snippets work too — pass <code>language="javascript"</code> or <code>"typescript"</code>.</CardDescription>
                </CardHeader>
                <CardContent>
                    <CodeBlock :code="jsSnippet" language="javascript" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Two-way binding</CardTitle>
                    <CardDescription>Use <code>v-model:language</code> to capture the user's pick. Current: <code>{{ pickedLanguage }}</code></CardDescription>
                </CardHeader>
                <CardContent>
                    <CodeBlock :code="sqlSnippet" v-model:language="pickedLanguage" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Single-line snippet</CardTitle>
                    <CardDescription>A long token or URL wraps inside the card instead of forcing horizontal scroll.</CardDescription>
                </CardHeader>
                <CardContent>
                    <CodeBlock code="sk_test_4eC39HqLyjWDarjtT1zdp7dc0eC39HqLyjWDarjtT1zd" language="plaintext" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Bash / shell commands</CardTitle>
                    <CardDescription>Common installer / deploy patterns.</CardDescription>
                </CardHeader>
                <CardContent>
                    <CodeBlock :code="bashSnippet" language="bash" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { CodeBlock } from '@/components/ui/code-block';
import type { LanguageId } from '@/components/ui/code-block/highlighter';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const pickedLanguage = ref<LanguageId>('auto');

const phpSnippet = `<?php

namespace App\\Services;

use App\\Models\\User;

class UserService
{
    public function findActive(int $id): ?User
    {
        return User::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->first();
    }
}`;

const jsSnippet = `import { ref, computed } from 'vue';

export function useCounter(initial = 0) {
    const count = ref(initial);
    const double = computed(() => count.value * 2);

    function increment() {
        count.value++;
    }

    return { count, double, increment };
}`;

const sqlSnippet = `SELECT u.id, u.email, COUNT(o.id) AS order_count
FROM users u
LEFT JOIN orders o ON o.user_id = u.id
WHERE u.created_at > NOW() - INTERVAL '30 days'
GROUP BY u.id, u.email
ORDER BY order_count DESC
LIMIT 10;`;

const bashSnippet = `# Install dependencies and start the dev server
composer install
pnpm install
php artisan migrate --seed
pnpm dev`;
</script>
