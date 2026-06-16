import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent, h } from 'vue';
import CustomizeColumns from '@/components/ui/data-table/CustomizeColumns.vue';

const ButtonStub = defineComponent({
    props: {
        label: {
            type: String,
            default: '',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['click'],
    setup(props, { emit, slots }) {
        return () => h('button', {
            disabled: props.disabled,
            onClick: (event: Event) => emit('click', event),
        }, props.label || slots.default?.());
    },
});

const PopoverStub = defineComponent({
    props: {
        open: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update:open'],
    setup(_, { slots }) {
        return () => h('div', slots.default?.());
    },
});

const InputStub = defineComponent({
    props: {
        modelValue: {
            type: String,
            default: '',
        },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        return () => h('input', {
            value: props.modelValue,
            onInput: (event: Event) => emit('update:modelValue', (event.target as HTMLInputElement).value),
        });
    },
});

const CheckboxStub = defineComponent({
    props: {
        modelValue: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['click', 'update:modelValue'],
    setup(props, { emit }) {
        return () => h('input', {
            type: 'checkbox',
            checked: props.modelValue,
            disabled: props.disabled,
            onClick: (event: Event) => emit('click', event),
        });
    },
});

const DraggableStub = defineComponent({
    props: {
        modelValue: {
            type: Array,
            default: () => [],
        },
    },
    setup(_, { slots }) {
        return () => h('div', slots.default?.());
    },
});

const findButtonByText = (wrapper: ReturnType<typeof mount>, text: string) => {
    return wrapper.findAll('button').find(button => button.text().includes(text));
};

describe('CustomizeColumns', () => {
    const columns = [
        { key: 'name', header: 'Name' },
        { key: 'email', header: 'Email' },
    ];

    const globalStubs = {
        Popover: PopoverStub,
        PopoverTrigger: PopoverStub,
        PopoverContent: PopoverStub,
        Button: ButtonStub,
        InputText: InputStub,
        Checkbox: CheckboxStub,
        VueDraggable: DraggableStub,
        Settings2: true,
        IconGripVertical: true,
    };

    it('emits default columns and default sort when reset is clicked', async () => {
        const wrapper = mount(CustomizeColumns, {
            props: {
                modelValue: ['email'],
                columns,
                defaultColumns: ['name', 'email'],
                sort: { column: 'email', direction: 'desc' },
                defaultSort: { column: 'name', direction: 'asc' },
            },
            global: { stubs: globalStubs },
        });

        const resetButton = findButtonByText(wrapper, 'Reset to default');
        expect(resetButton).toBeTruthy();

        await resetButton!.trigger('click');

        expect(wrapper.emitted('update:modelValue')?.[0]?.[0]).toEqual(['name', 'email']);
        expect(wrapper.emitted('update:sort')?.[0]?.[0]).toEqual({ column: 'name', direction: 'asc' });
    });

    it('emits null sort when defaultSort is not provided', async () => {
        const wrapper = mount(CustomizeColumns, {
            props: {
                modelValue: ['email'],
                columns,
                defaultColumns: ['name', 'email'],
                sort: { column: 'email', direction: 'desc' },
            },
            global: { stubs: globalStubs },
        });

        const resetButton = findButtonByText(wrapper, 'Reset to default');
        expect(resetButton).toBeTruthy();

        await resetButton!.trigger('click');

        expect(wrapper.emitted('update:modelValue')?.[0]?.[0]).toEqual(['name', 'email']);
        expect(wrapper.emitted('update:sort')?.[0]?.[0]).toEqual({ column: null, direction: null });
    });
});
