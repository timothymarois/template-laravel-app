import { onMounted, onUnmounted, ref } from 'vue';

/**
 * Get the Echo instance
 * @returns {Echo} The global Echo instance
 */
export const getEcho = () => {
    if (typeof window === 'undefined' || !window.Echo) {
        return null;
    }
    return window.Echo;
};

/**
 * Subscribe to a public channel with auto-cleanup
 * @param {string} channelName - The channel name to subscribe to
 * @returns {{ channel: Ref, leave: Function }}
 */
export const useChannel = (channelName) => {
    const channel = ref(null);
    const echo = getEcho();

    onMounted(() => {
        if (echo && channelName) {
            channel.value = echo.channel(channelName);
        }
    });

    onUnmounted(() => {
        if (echo && channelName) {
            echo.leave(channelName);
            channel.value = null;
        }
    });

    const leave = () => {
        if (echo && channelName) {
            echo.leave(channelName);
            channel.value = null;
        }
    };

    return { channel, leave };
};

/**
 * Subscribe to a private channel with auto-cleanup
 * @param {string} channelName - The channel name to subscribe to
 * @returns {{ channel: Ref, leave: Function }}
 */
export const usePrivateChannel = (channelName) => {
    const channel = ref(null);
    const echo = getEcho();

    onMounted(() => {
        if (echo && channelName) {
            channel.value = echo.private(channelName);
        }
    });

    onUnmounted(() => {
        if (echo && channelName) {
            echo.leave(`private-${channelName}`);
            channel.value = null;
        }
    });

    const leave = () => {
        if (echo && channelName) {
            echo.leave(`private-${channelName}`);
            channel.value = null;
        }
    };

    return { channel, leave };
};

/**
 * Subscribe to a presence channel with auto-cleanup
 * @param {string} channelName - The channel name to subscribe to
 * @returns {{ channel: Ref, leave: Function }}
 */
export const usePresenceChannel = (channelName) => {
    const channel = ref(null);
    const echo = getEcho();

    onMounted(() => {
        if (echo && channelName) {
            channel.value = echo.join(channelName);
        }
    });

    onUnmounted(() => {
        if (echo && channelName) {
            echo.leave(`presence-${channelName}`);
            channel.value = null;
        }
    });

    const leave = () => {
        if (echo && channelName) {
            echo.leave(`presence-${channelName}`);
            channel.value = null;
        }
    };

    return { channel, leave };
};

/**
 * Listen to an event on a channel (simplified helper)
 * @param {string} channelName - The channel name
 * @param {string} eventName - The event to listen for
 * @param {Function} callback - Callback when event is received
 * @param {{ private?: boolean, presence?: boolean }} options - Channel options
 */
export const useListen = (channelName, eventName, callback, options = {}) => {
    const echo = getEcho();

    onMounted(() => {
        if (!echo || !channelName) return;

        let channel;
        if (options.presence) {
            channel = echo.join(channelName);
        } else if (options.private) {
            channel = echo.private(channelName);
        } else {
            channel = echo.channel(channelName);
        }

        channel.listen(eventName, callback);
    });

    onUnmounted(() => {
        if (!echo || !channelName) return;

        const prefix = options.presence ? 'presence-' : options.private ? 'private-' : '';
        echo.leave(`${prefix}${channelName}`);
    });
};
