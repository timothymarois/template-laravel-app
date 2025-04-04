import { ref, computed, watchEffect } from 'vue';

export function useTheme() {
    const auto = ref(false);
    const dark = ref(false);

    const isDark = computed(() => dark.value);

    const checkSystemTheme = () => window.matchMedia('(prefers-color-scheme: dark)').matches;

    const updateDocumentClass = () => {
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

    watchEffect(() => {
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
