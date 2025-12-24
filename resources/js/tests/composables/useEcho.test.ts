import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent, nextTick } from 'vue';
import { getEcho, useChannel, usePrivateChannel, usePresenceChannel, useListen } from '@/composables/useEcho';

describe('useEcho', () => {
    let mockChannel: { listen: ReturnType<typeof vi.fn> };
    let mockEcho: {
        channel: ReturnType<typeof vi.fn>;
        private: ReturnType<typeof vi.fn>;
        join: ReturnType<typeof vi.fn>;
        leave: ReturnType<typeof vi.fn>;
    };

    beforeEach(() => {
        mockChannel = {
            listen: vi.fn().mockReturnThis(),
        };

        mockEcho = {
            channel: vi.fn().mockReturnValue(mockChannel),
            private: vi.fn().mockReturnValue(mockChannel),
            join: vi.fn().mockReturnValue(mockChannel),
            leave: vi.fn(),
        };

        // @ts-expect-error - mocking window.Echo
        window.Echo = mockEcho;
    });

    afterEach(() => {
        vi.clearAllMocks();
        // @ts-expect-error - cleaning up mock
        delete window.Echo;
    });

    describe('getEcho', () => {
        it('returns Echo instance when available', () => {
            expect(getEcho()).toBe(mockEcho);
        });

        it('returns null when Echo is not available', () => {
            // @ts-expect-error - cleaning up mock
            delete window.Echo;
            expect(getEcho()).toBeNull();
        });
    });

    describe('useChannel', () => {
        it('subscribes to channel on mount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    const { channel } = useChannel('test-channel');
                    return { channel };
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.channel).toHaveBeenCalledWith('test-channel');
        });

        it('leaves channel on unmount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    useChannel('test-channel');
                    return {};
                },
                template: '<div></div>',
            });

            const wrapper = mount(TestComponent);
            await nextTick();

            wrapper.unmount();

            expect(mockEcho.leave).toHaveBeenCalledWith('test-channel');
        });

        it('provides leave function for manual cleanup', async () => {
            const TestComponent = defineComponent({
                setup() {
                    const { leave } = useChannel('test-channel');
                    return { leave };
                },
                template: '<div></div>',
            });

            const wrapper = mount(TestComponent);
            await nextTick();

            wrapper.vm.leave();

            expect(mockEcho.leave).toHaveBeenCalledWith('test-channel');
        });

        it('does not subscribe when channelName is empty', async () => {
            const TestComponent = defineComponent({
                setup() {
                    useChannel('');
                    return {};
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.channel).not.toHaveBeenCalled();
        });
    });

    describe('usePrivateChannel', () => {
        it('subscribes to private channel on mount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    usePrivateChannel('user.1');
                    return {};
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.private).toHaveBeenCalledWith('user.1');
        });

        it('leaves private channel on unmount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    usePrivateChannel('user.1');
                    return {};
                },
                template: '<div></div>',
            });

            const wrapper = mount(TestComponent);
            await nextTick();

            wrapper.unmount();

            expect(mockEcho.leave).toHaveBeenCalledWith('private-user.1');
        });
    });

    describe('usePresenceChannel', () => {
        it('joins presence channel on mount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    usePresenceChannel('chat.1');
                    return {};
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.join).toHaveBeenCalledWith('chat.1');
        });

        it('leaves presence channel on unmount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    usePresenceChannel('chat.1');
                    return {};
                },
                template: '<div></div>',
            });

            const wrapper = mount(TestComponent);
            await nextTick();

            wrapper.unmount();

            expect(mockEcho.leave).toHaveBeenCalledWith('presence-chat.1');
        });
    });

    describe('useListen', () => {
        it('listens to event on public channel', async () => {
            const callback = vi.fn();

            const TestComponent = defineComponent({
                setup() {
                    useListen('orders', 'OrderShipped', callback);
                    return {};
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.channel).toHaveBeenCalledWith('orders');
            expect(mockChannel.listen).toHaveBeenCalledWith('OrderShipped', callback);
        });

        it('listens to event on private channel', async () => {
            const callback = vi.fn();

            const TestComponent = defineComponent({
                setup() {
                    useListen('orders', 'OrderShipped', callback, { private: true });
                    return {};
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.private).toHaveBeenCalledWith('orders');
            expect(mockChannel.listen).toHaveBeenCalledWith('OrderShipped', callback);
        });

        it('listens to event on presence channel', async () => {
            const callback = vi.fn();

            const TestComponent = defineComponent({
                setup() {
                    useListen('chat', 'MessageSent', callback, { presence: true });
                    return {};
                },
                template: '<div></div>',
            });

            mount(TestComponent);
            await nextTick();

            expect(mockEcho.join).toHaveBeenCalledWith('chat');
            expect(mockChannel.listen).toHaveBeenCalledWith('MessageSent', callback);
        });

        it('leaves channel on unmount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    useListen('orders', 'OrderShipped', vi.fn());
                    return {};
                },
                template: '<div></div>',
            });

            const wrapper = mount(TestComponent);
            await nextTick();

            wrapper.unmount();

            expect(mockEcho.leave).toHaveBeenCalledWith('orders');
        });

        it('leaves private channel with prefix on unmount', async () => {
            const TestComponent = defineComponent({
                setup() {
                    useListen('orders', 'OrderShipped', vi.fn(), { private: true });
                    return {};
                },
                template: '<div></div>',
            });

            const wrapper = mount(TestComponent);
            await nextTick();

            wrapper.unmount();

            expect(mockEcho.leave).toHaveBeenCalledWith('private-orders');
        });
    });
});
