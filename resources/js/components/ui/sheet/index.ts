import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Sheet } from "./Sheet.vue"
export { default as SheetClose } from "./SheetClose.vue"
export { default as SheetContent } from "./SheetContent.vue"
export { default as SheetDescription } from "./SheetDescription.vue"
export { default as SheetFooter } from "./SheetFooter.vue"
export { default as SheetForm } from "./SheetForm.vue"
export { default as SheetHeader } from "./SheetHeader.vue"
export { default as SheetTitle } from "./SheetTitle.vue"
export { default as SheetTrigger } from "./SheetTrigger.vue"

export const sheetVariants = cva(
    "fixed z-50 bg-background p-6",
    {
        variants: {
            side: {
                top: "inset-x-0 top-0 border-b shadow-lg",
                bottom: "inset-x-0 bottom-0 border-t shadow-lg",
                left: "inset-y-0 left-0 h-full w-full border-r shadow-lg sm:max-w-lg",
                right: "inset-y-0 right-0 h-full w-full border-l shadow-[-4px_0_16px_rgba(0,0,0,0.15)] sm:max-w-lg",
            },
        },
        defaultVariants: {
            side: "right",
        },
    },
)

export type SheetVariants = VariantProps<typeof sheetVariants>
