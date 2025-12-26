import { contextBridge, ipcRenderer } from 'electron';

// Expose loading window API to renderer
contextBridge.exposeInMainWorld('loadingAPI', {
    onBootStatus: (callback: (data: { status: string; progress: number }) => void) => {
        ipcRenderer.on('boot-status', (_event, data) => callback(data));
    },
    onBootError: (callback: (data: { error: string }) => void) => {
        ipcRenderer.on('boot-error', (_event, data) => callback(data));
    },
    retryBoot: () => ipcRenderer.invoke('retry-boot'),
    quitApp: () => ipcRenderer.send('quit-app'),
});

// Type declaration for the API
declare global {
    interface Window {
        loadingAPI: {
            onBootStatus: (callback: (data: { status: string; progress: number }) => void) => void;
            onBootError: (callback: (data: { error: string }) => void) => void;
            retryBoot: () => Promise<{ success: boolean; error?: string }>;
            quitApp: () => void;
        };
    }
}
