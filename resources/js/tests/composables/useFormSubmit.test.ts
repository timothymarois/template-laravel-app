import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useFormSubmit } from '@/composables/useFormSubmit';

// Mock vue-sonner
vi.mock('vue-sonner', () => ({
    toast: {
        success: vi.fn(),
    },
}));

import { toast } from 'vue-sonner';

describe('useFormSubmit', () => {
    let formSubmit: ReturnType<typeof useFormSubmit>;
    let mockForm: { post: ReturnType<typeof vi.fn>; get: ReturnType<typeof vi.fn>; put: ReturnType<typeof vi.fn>; delete: ReturnType<typeof vi.fn> };

    beforeEach(() => {
        vi.clearAllMocks();

        formSubmit = useFormSubmit();

        mockForm = {
            post: vi.fn().mockResolvedValue(undefined),
            get: vi.fn().mockResolvedValue(undefined),
            put: vi.fn().mockResolvedValue(undefined),
            delete: vi.fn().mockResolvedValue(undefined),
        };
    });

    describe('submitForm', () => {
        it('calls form method with correct route', async () => {
            await formSubmit.submitForm(mockForm, 'post', '/test-route');

            expect(mockForm.post).toHaveBeenCalledWith('/test-route', expect.any(Object));
        });

        it('uses lowercase method name', async () => {
            await formSubmit.submitForm(mockForm, 'POST', '/test-route');

            expect(mockForm.post).toHaveBeenCalled();
        });

        it('supports different HTTP methods', async () => {
            await formSubmit.submitForm(mockForm, 'get', '/route');
            expect(mockForm.get).toHaveBeenCalled();

            await formSubmit.submitForm(mockForm, 'put', '/route');
            expect(mockForm.put).toHaveBeenCalled();

            await formSubmit.submitForm(mockForm, 'delete', '/route');
            expect(mockForm.delete).toHaveBeenCalled();
        });

        it('passes default options', async () => {
            await formSubmit.submitForm(mockForm, 'post', '/route');

            const options = mockForm.post.mock.calls[0][1];
            expect(options.preserveScroll).toBe(true);
            expect(options.preserveState).toBe(true);
            expect(options.data).toEqual({});
            expect(options.replace).toBe(false);
            expect(options.only).toEqual([]);
            expect(options.except).toEqual([]);
        });

        it('allows overriding default options', async () => {
            await formSubmit.submitForm(mockForm, 'post', '/route', {
                preserveScroll: false,
                preserveState: false,
                data: { key: 'value' },
                replace: true,
                only: ['field1'],
                except: ['field2'],
            });

            const options = mockForm.post.mock.calls[0][1];
            expect(options.preserveScroll).toBe(false);
            expect(options.preserveState).toBe(false);
            expect(options.data).toEqual({ key: 'value' });
            expect(options.replace).toBe(true);
            expect(options.only).toEqual(['field1']);
            expect(options.except).toEqual(['field2']);
        });

        it('shows toast on success when toastMessage provided', async () => {
            // Setup mock to call onSuccess
            mockForm.post.mockImplementation(async (_route, options) => {
                options.onSuccess({ url: '/dashboard' });
            });

            await formSubmit.submitForm(mockForm, 'post', '/route', {
                toastMessage: 'Success!',
            });

            expect(toast.success).toHaveBeenCalledWith('Success!');
        });

        it('does not show toast when redirected to login', async () => {
            mockForm.post.mockImplementation(async (_route, options) => {
                options.onSuccess({ url: '/login' });
            });

            await formSubmit.submitForm(mockForm, 'post', '/route', {
                toastMessage: 'Success!',
            });

            expect(toast.success).not.toHaveBeenCalled();
        });

        it('does not show toast when no toastMessage', async () => {
            mockForm.post.mockImplementation(async (_route, options) => {
                options.onSuccess({ url: '/dashboard' });
            });

            await formSubmit.submitForm(mockForm, 'post', '/route');

            expect(toast.success).not.toHaveBeenCalled();
        });

        it('calls custom onSuccess callback', async () => {
            const onSuccess = vi.fn();
            const response = { url: '/dashboard' };

            mockForm.post.mockImplementation(async (_route, options) => {
                options.onSuccess(response);
            });

            await formSubmit.submitForm(mockForm, 'post', '/route', { onSuccess });

            expect(onSuccess).toHaveBeenCalledWith(response);
        });

        it('passes onError callback', async () => {
            const onError = vi.fn();

            await formSubmit.submitForm(mockForm, 'post', '/route', { onError });

            const options = mockForm.post.mock.calls[0][1];
            expect(options.onError).toBe(onError);
        });

        it('passes onFinish callback', async () => {
            const onFinish = vi.fn();

            await formSubmit.submitForm(mockForm, 'post', '/route', { onFinish });

            const options = mockForm.post.mock.calls[0][1];
            expect(options.onFinish).toBe(onFinish);
        });

        it('passes onBefore callback', async () => {
            const onBefore = vi.fn();

            await formSubmit.submitForm(mockForm, 'post', '/route', { onBefore });

            const options = mockForm.post.mock.calls[0][1];
            expect(options.onBefore).toBe(onBefore);
        });

        it('passes onProgress callback', async () => {
            const onProgress = vi.fn();

            await formSubmit.submitForm(mockForm, 'post', '/route', { onProgress });

            const options = mockForm.post.mock.calls[0][1];
            expect(options.onProgress).toBe(onProgress);
        });
    });
});
