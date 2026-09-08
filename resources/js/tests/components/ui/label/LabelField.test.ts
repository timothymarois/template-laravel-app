import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import LabelField from '@/components/ui/label/LabelField.vue';

describe('LabelField', () => {
    it('renders help text under the field', () => {
        // Undeclared, a passed `help` falls through onto the wrapper and renders
        // nowhere — silently, with no warning. A fork lost ~30 explanations that way.
        const wrapper = mount(LabelField, { props: { label: 'Email', help: 'We never share it.' } });

        expect(wrapper.text()).toContain('We never share it.');
    });

    it('shows the error instead of the help, not both', () => {
        const wrapper = mount(LabelField, {
            props: { label: 'Email', help: 'We never share it.', error: 'Email is required.' },
        });

        expect(wrapper.text()).toContain('Email is required.');
        expect(wrapper.text()).not.toContain('We never share it.');
    });

    it('renders neither when neither is given', () => {
        const wrapper = mount(LabelField, { props: { label: 'Email' } });

        expect(wrapper.find('.text-muted-foreground').exists()).toBe(false);
        expect(wrapper.find('.text-destructive').exists()).toBe(false);
    });

    it('accepts an object or array class, not only a string', () => {
        const wrapper = mount(LabelField, { props: { label: 'x', class: { 'is-wide': true } } });

        expect(wrapper.classes()).toContain('is-wide');
    });
});
