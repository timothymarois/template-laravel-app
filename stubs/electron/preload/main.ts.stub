import { contextBridge, ipcRenderer } from 'electron';

// Expose main window API to renderer
contextBridge.exposeInMainWorld('electronAPI', {
    // Shell operations
    shell: {
        openExternal: (url: string) => ipcRenderer.invoke('shell:openExternal', url),
        showItemInFolder: (path: string) => ipcRenderer.invoke('shell:showItemInFolder', path),
    },

    // Dialog operations
    dialog: {
        openFile: (options: Electron.OpenDialogOptions) => ipcRenderer.invoke('dialog:openFile', options),
        openDirectory: (options: Electron.OpenDialogOptions) => ipcRenderer.invoke('dialog:openDirectory', options),
        saveFile: (options: Electron.SaveDialogOptions) => ipcRenderer.invoke('dialog:saveFile', options),
    },

    // App info
    app: {
        getInfo: () => ipcRenderer.invoke('app:getInfo'),
        getPath: (name: string) => ipcRenderer.invoke('app:getPath', name),
        quit: () => ipcRenderer.send('quit-app'),
    },

    // Path utilities
    path: {
        join: (...segments: string[]) => ipcRenderer.invoke('path:join', ...segments),
    },
});

// Type declaration for the API
declare global {
    interface Window {
        electronAPI: {
            shell: {
                openExternal: (url: string) => Promise<void>;
                showItemInFolder: (path: string) => Promise<void>;
            };
            dialog: {
                openFile: (options: Electron.OpenDialogOptions) => Promise<Electron.OpenDialogReturnValue>;
                openDirectory: (options: Electron.OpenDialogOptions) => Promise<Electron.OpenDialogReturnValue>;
                saveFile: (options: Electron.SaveDialogOptions) => Promise<Electron.SaveDialogReturnValue>;
            };
            app: {
                getInfo: () => Promise<{
                    name: string;
                    version: string;
                    isPackaged: boolean;
                    paths: Record<string, string>;
                }>;
                getPath: (name: string) => Promise<string | null>;
                quit: () => void;
            };
            path: {
                join: (...segments: string[]) => Promise<string>;
            };
        };
    }
}
