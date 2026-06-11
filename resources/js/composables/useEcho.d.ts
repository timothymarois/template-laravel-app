import type { Ref } from 'vue';

export interface EchoChannel {
    listen(eventName: string, callback: (event: unknown) => void): EchoChannel;
    stopListening(eventName: string): EchoChannel;
}

export interface EchoClient {
    channel(channelName: string): EchoChannel;
    private(channelName: string): EchoChannel;
    join(channelName: string): EchoChannel;
    leave(channelName: string): void;
}

export function getEcho(): EchoClient | null;

export function useChannel(channelName: string): {
    channel: Ref<EchoChannel | null>;
    leave: () => void;
};

export function usePrivateChannel(channelName: string): {
    channel: Ref<EchoChannel | null>;
    leave: () => void;
};

export function usePresenceChannel(channelName: string): {
    channel: Ref<EchoChannel | null>;
    leave: () => void;
};

export function useListen(
    channelName: string,
    eventName: string,
    callback: (event: unknown) => void,
    options?: {
        private?: boolean;
        presence?: boolean;
    },
): void;
