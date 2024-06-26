import { defineStore } from 'pinia';

export const useThemeStore = defineStore("theme-store", {
    state: () => ({
        auto: false,
        dark: false
    }),
    getters: {
        isDark: (state) => state.dark,
    },
    actions: {
        checkSystemTheme() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        },
        updateDocumentClass() {
            const className = 'dark';
            const classList = document.documentElement.classList;
            if (this.dark && !classList.contains(className)) {
                classList.add(className);
            } else if (!this.dark && classList.contains(className)) {
                classList.remove(className);
            }
        },
        setDark(v) {
            this.dark = v;
            this.updateDocumentClass();
            if (this.checkSystemTheme() === this.dark) {
                this.auto = true
            }
            else {
                this.auto = false
            }
        },
        toggleDark() {
            this.setDark(!this.dark)
        },
        initTheme() {
            if (this.auto) {
                this.setDark(this.checkSystemTheme());
            } else {
                this.setDark(this.dark);
            }
        }
    },
    persist: {
        key: 'ui-theme'
    }
});
