// https://inertiajs.com/manual-visits

import { useToast } from 'primevue/usetoast';

export const useFormSubmit = () => {
    const toast = useToast();
    const submitForm = async (
        form,
        method = 'post',
        routePath,
        {
            preserveScroll = true,
            preserveState = true,
            data = {},
            replace = false,
            only = [],
            except = [],
            onSuccess = () => {},
            onError = () => {},
            onFinish = () => {},
            onBefore = () => {},
            onProgress = () => {},
            toastMessage = null,
        } = {}
    ) => {
        const options = {
            preserveScroll,
            preserveState,
            data,
            replace,
            only,
            except,
            onSuccess: (response) => {

                // ideally, this should be a failed response :(
                if (response?.url !== '/login') {
                    if (toastMessage) {
                        toast.add({ severity: 'success', summary: toastMessage, life: 6000 });
                    }
                }

                onSuccess(response);
            },
            onError,
            onFinish,
            onBefore,
            onProgress,
        };

        await form[method.toLowerCase()](routePath, options);
    };

    return {
        submitForm
    };
};
