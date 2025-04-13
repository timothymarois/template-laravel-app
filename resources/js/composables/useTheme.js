import { ref, computed, watchEffect, onMounted } from 'vue';

export function useTheme() {
    const auto = ref(false);
    const dark = ref(false);
    const isDark = computed(() => dark.value);

    const checkSystemTheme = () => {
        if (typeof window === 'undefined') return false;
        return window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
    };

    const updateDocumentClass = () => {
        if (typeof document === 'undefined') return;

        const className = 'dark';
        const classList = document.documentElement.classList;

        if (dark.value && !classList.contains(className)) {
            classList.add(className);
        } else if (!dark.value && classList.contains(className)) {
            classList.remove(className);
        }
    };

    const setDark = (value) => {
        dark.value = value;
        updateDocumentClass();
        auto.value = checkSystemTheme() === dark.value;
    };

    const toggleDark = () => setDark(!dark.value);

    const initTheme = () => {
        if (auto.value) {
            setDark(checkSystemTheme());
        } else {
            setDark(dark.value);
        }
    };

    onMounted(() => {
        initTheme();
    });

    return {
        auto,
        dark,
        isDark,
        setDark,
        toggleDark,
        initTheme,
    };
}
