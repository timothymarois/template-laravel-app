export default {
    root: {
        class: [
            //Shape
            'rounded',
            'shadow',

            //Color
            'bg-surface-0 dark:bg-surface-800',
            'text-surface-700 dark:text-surface-0'
        ]
    },
    body: {
        class: 'p-5'
    },
    title: {
        class: 'text-xl font-semibold mb-2'
    },
    subtitle: {
        class: [
            //Font
            'font-normal',

            //Spacing
            'mb-4',

            //Color
            'text-surface-600 dark:text-surface-0/60'
        ]
    },
    content: {
        // class: 'py-5' // Vertical padding.
    },
    footer: {
        // class: 'pt-5' // Top padding.
    }
};
