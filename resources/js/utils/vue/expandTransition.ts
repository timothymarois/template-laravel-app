/**
 * Creates transition hooks for smooth height-based expand/collapse animations.
 * Use with Vue's <transition> component's @enter and @leave events.
 *
 * @param duration - Animation duration in milliseconds (default: 200)
 * @returns Object with expandEnter and expandLeave functions
 *
 * @example
 * const { expandEnter, expandLeave } = createExpandTransition(300);
 *
 * <transition @enter="expandEnter" @leave="expandLeave">
 *   <div v-show="isOpen">Content</div>
 * </transition>
 */
export function createExpandTransition(duration: number = 200) {
    const expandEnter = (el: HTMLElement) => {
        el.style.height = '0';
        el.style.overflow = 'hidden';
        // Force reflow to ensure transition triggers
        void el.offsetHeight;
        el.style.transition = `height ${duration}ms ease`;
        el.style.height = `${el.scrollHeight}px`;
    };

    const expandLeave = (el: HTMLElement) => {
        el.style.height = `${el.scrollHeight}px`;
        el.style.overflow = 'hidden';
        // Force reflow to ensure transition triggers
        void el.offsetHeight;
        el.style.transition = `height ${duration}ms ease`;
        el.style.height = '0px';
    };

    const expandAfterEnter = (el: HTMLElement) => {
        el.style.height = 'auto';
        el.style.overflow = '';
        el.style.transition = '';
    };

    const expandAfterLeave = (el: HTMLElement) => {
        el.style.height = '';
        el.style.overflow = '';
        el.style.transition = '';
    };

    return {
        expandEnter,
        expandLeave,
        expandAfterEnter,
        expandAfterLeave,
    };
}
