import { ptViewMerge } from './ptViewMerge';

export const ptMerge = (
    base: Record<string, any> = {},
    override: Record<string, any> = {}
): Record<string, any> => {
    const keys = new Set([...Object.keys(base ?? {}), ...Object.keys(override ?? {})]);
    const out: Record<string, any> = {};
    const normalize = (val: any) => (typeof val === 'string' ? { class: val } : val);
    keys.forEach((key) => {
        out[key] = ptViewMerge(normalize(base?.[key]), normalize(override?.[key]));
    });
    return out;
};
