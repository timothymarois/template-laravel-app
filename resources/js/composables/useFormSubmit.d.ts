export type FormSubmitForm = Record<string, any>;

export interface FormSubmitResponse {
    url?: string;
}

export interface FormSubmitOptions {
    preserveScroll?: boolean;
    preserveState?: boolean;
    data?: Record<string, unknown>;
    replace?: boolean;
    only?: string[];
    except?: string[];
    onSuccess?: (response: FormSubmitResponse) => void;
    onError?: (...args: unknown[]) => void;
    onFinish?: (...args: unknown[]) => void;
    onBefore?: (...args: unknown[]) => void;
    onProgress?: (...args: unknown[]) => void;
    toastMessage?: string | null;
}

export function useFormSubmit(): {
    submitForm: (
        form: FormSubmitForm,
        method: 'get' | 'post' | 'put' | 'patch' | 'delete' | 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE',
        routePath: string,
        options?: FormSubmitOptions,
    ) => Promise<void>;
};
