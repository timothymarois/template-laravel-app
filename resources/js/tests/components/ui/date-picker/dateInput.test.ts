import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent, h, nextTick } from 'vue';
import { CalendarDate } from '@internationalized/date';
import DateInput from '@/components/ui/date-picker/DateInput.vue';

const InputStub = defineComponent({
    props: {
        modelValue: {
            type: String,
            default: '',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['input', 'blur'],
    setup(props, { emit }) {
        const onInput = (event: Event) => emit('input', event);
        const onBlur = (event: Event) => emit('blur', event);

        return () => h('input', {
            value: props.modelValue ?? '',
            disabled: props.disabled,
            onInput,
            onBlur,
        });
    },
});

const PopoverStub = defineComponent({
    setup(_, { slots }) {
        return () => h('div', slots.default?.());
    },
});

const calendarSelectDate = new CalendarDate(2024, 2, 3);

const CalendarStub = defineComponent({
    emits: ['update:model-value'],
    setup(_, { emit }) {
        return () => h('button', {
            'data-test': 'calendar-select',
            onClick: () => emit('update:model-value', calendarSelectDate),
        }, 'Select');
    },
});

const globalStubs = {
    Input: InputStub,
    PopoverBase: PopoverStub,
    PopoverTrigger: PopoverStub,
    PopoverContent: PopoverStub,
    Calendar: CalendarStub,
    CalendarIcon: true,
    X: true,
};

describe('DateInput', () => {
    it('formats input and emits when a valid date is entered', async () => {
        const wrapper = mount(DateInput, {
            global: { stubs: globalStubs },
        });

        const input = wrapper.find('input');
        await input.setValue('01022024');
        await nextTick();

        const emitted = wrapper.emitted('update:modelValue');
        expect(emitted).toBeTruthy();
        const payload = emitted?.[0]?.[0] as CalendarDate;
        expect(payload.year).toBe(2024);
        expect(payload.month).toBe(1);
        expect(payload.day).toBe(2);
        expect((input.element as HTMLInputElement).value).toBe('01/02/2024');
    });

    it('blocks disabled dates and shows error state', async () => {
        const wrapper = mount(DateInput, {
            props: {
                isDateDisabled: () => true,
            },
            global: { stubs: globalStubs },
        });

        const input = wrapper.find('input');
        await input.setValue('01022024');
        await nextTick();

        expect(wrapper.emitted('update:modelValue')).toBeUndefined();
        expect(wrapper.find('.text-destructive').exists()).toBe(true);
    });

    it('marks incomplete input as invalid on blur', async () => {
        const wrapper = mount(DateInput, {
            global: { stubs: globalStubs },
        });

        const input = wrapper.find('input');
        await input.setValue('0102');
        await input.trigger('blur');
        await nextTick();

        expect(wrapper.find('.text-destructive').exists()).toBe(true);
    });

    it('syncs display when modelValue changes', async () => {
        const wrapper = mount(DateInput, {
            props: {
                modelValue: new CalendarDate(2024, 1, 2),
            },
            global: { stubs: globalStubs },
        });

        const input = wrapper.find('input');
        expect((input.element as HTMLInputElement).value).toBe('01/02/2024');

        await wrapper.setProps({ modelValue: new CalendarDate(2025, 12, 31) });
        await nextTick();

        expect((input.element as HTMLInputElement).value).toBe('12/31/2025');
    });

    it('clears the input and emits undefined when clear is clicked', async () => {
        const wrapper = mount(DateInput, {
            props: {
                clearable: true,
                modelValue: new CalendarDate(2024, 1, 2),
            },
            global: { stubs: globalStubs },
        });

        const clearButton = wrapper.find('button.px-2');
        await clearButton.trigger('click');
        await nextTick();

        const emitted = wrapper.emitted('update:modelValue');
        expect(emitted?.[0]?.[0]).toBeUndefined();
        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('');
    });

    it('updates value when a calendar date is selected', async () => {
        const wrapper = mount(DateInput, {
            global: { stubs: globalStubs },
        });

        await wrapper.find('[data-test="calendar-select"]').trigger('click');
        await nextTick();

        const emitted = wrapper.emitted('update:modelValue');
        expect(emitted).toBeTruthy();
        const payload = emitted?.[0]?.[0] as CalendarDate;
        expect(payload.year).toBe(2024);
        expect(payload.month).toBe(2);
        expect(payload.day).toBe(3);
        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('02/03/2024');
    });
});
