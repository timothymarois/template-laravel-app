import { BrowserWindow, screen } from 'electron';
import * as path from 'path';

/**
 * Create the loading window shown during boot sequence.
 */
export function createLoadingWindow(): BrowserWindow {
    const primaryDisplay = screen.getPrimaryDisplay();
    const { width, height } = primaryDisplay.workAreaSize;

    const loadingWindow = new BrowserWindow({
        width: 400,
        height: 300,
        frame: false,
        transparent: true,
        resizable: false,
        movable: true,
        minimizable: false,
        maximizable: false,
        alwaysOnTop: true,
        center: true,
        show: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, '../preload/loading.js'),
        },
    });

    // Center on primary display
    loadingWindow.setPosition(
        Math.round((width - 400) / 2),
        Math.round((height - 300) / 2)
    );

    // Load the loading screen HTML
    loadingWindow.loadFile(path.join(__dirname, '../renderer/loading.html'));

    return loadingWindow;
}

/**
 * Create the main application window.
 */
export function createMainWindow(url: string): BrowserWindow {
    const mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,
        minWidth: 800,
        minHeight: 600,
        show: false,
        titleBarStyle: 'hiddenInset',
        trafficLightPosition: { x: 16, y: 16 },
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, '../preload/main.js'),
            spellcheck: true,
        },
    });

    // Load the Laravel application
    mainWindow.loadURL(url);

    // Show window when ready
    mainWindow.once('ready-to-show', () => {
        mainWindow.show();
    });

    // Open external links in default browser
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url.startsWith('http://') || url.startsWith('https://')) {
            require('electron').shell.openExternal(url);
            return { action: 'deny' };
        }
        return { action: 'allow' };
    });

    return mainWindow;
}
