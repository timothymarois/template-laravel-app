<template>
    <LayoutApp
        title="Components - Carousel"
        pageTitle="Carousel"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Carousel -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic Carousel</CardTitle>
                    <CardDescription>A simple horizontal carousel with navigation buttons</CardDescription>
                </CardHeader>
                <CardContent>
                    <Carousel class="w-full max-w-xs mx-auto">
                        <CarouselContent>
                            <CarouselItem v-for="i in 5" :key="i">
                                <div class="p-1">
                                    <Card>
                                        <CardContent class="flex aspect-square items-center justify-center p-6">
                                            <span class="text-4xl font-semibold">{{ i }}</span>
                                        </CardContent>
                                    </Card>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious />
                        <CarouselNext />
                    </Carousel>
                </CardContent>
            </Card>

            <!-- Carousel Sizes -->
            <Card>
                <CardHeader>
                    <CardTitle>Carousel Sizes</CardTitle>
                    <CardDescription>Different carousel widths and item basis configurations</CardDescription>
                </CardHeader>
                <CardContent class="space-y-8">
                    <div>
                        <p class="text-sm font-medium mb-3">1/3 Width Items</p>
                        <Carousel class="w-full max-w-sm mx-auto">
                            <CarouselContent>
                                <CarouselItem v-for="i in 9" :key="i" class="basis-1/3">
                                    <div class="p-1">
                                        <Card>
                                            <CardContent class="flex aspect-square items-center justify-center p-2">
                                                <span class="text-2xl font-semibold">{{ i }}</span>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious />
                            <CarouselNext />
                        </Carousel>
                    </div>
                    <div>
                        <p class="text-sm font-medium mb-3">1/2 Width Items</p>
                        <Carousel class="w-full max-w-md mx-auto">
                            <CarouselContent>
                                <CarouselItem v-for="i in 6" :key="i" class="basis-1/2">
                                    <div class="p-1">
                                        <Card>
                                            <CardContent class="flex aspect-square items-center justify-center p-4">
                                                <span class="text-3xl font-semibold">{{ i }}</span>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious />
                            <CarouselNext />
                        </Carousel>
                    </div>
                </CardContent>
            </Card>

            <!-- Vertical Carousel -->
            <Card>
                <CardHeader>
                    <CardTitle>Vertical Carousel</CardTitle>
                    <CardDescription>A carousel that scrolls vertically</CardDescription>
                </CardHeader>
                <CardContent>
                    <Carousel orientation="vertical" class="w-full max-w-xs mx-auto">
                        <CarouselContent class="-mt-1 h-[200px]">
                            <CarouselItem v-for="i in 5" :key="i" class="pt-1 basis-1/2">
                                <div class="p-1">
                                    <Card>
                                        <CardContent class="flex items-center justify-center p-6">
                                            <span class="text-3xl font-semibold">{{ i }}</span>
                                        </CardContent>
                                    </Card>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious />
                        <CarouselNext />
                    </Carousel>
                </CardContent>
            </Card>

            <!-- Image Carousel -->
            <Card>
                <CardHeader>
                    <CardTitle>Image Carousel</CardTitle>
                    <CardDescription>Carousel with placeholder images</CardDescription>
                </CardHeader>
                <CardContent>
                    <Carousel class="w-full max-w-lg mx-auto">
                        <CarouselContent>
                            <CarouselItem v-for="(image, index) in images" :key="index">
                                <div class="p-1">
                                    <div class="overflow-hidden rounded-lg">
                                        <img
                                            :src="image.url"
                                            :alt="image.alt"
                                            class="w-full aspect-video object-cover"
                                        />
                                    </div>
                                    <p class="mt-2 text-sm text-center text-muted-foreground">{{ image.alt }}</p>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious />
                        <CarouselNext />
                    </Carousel>
                </CardContent>
            </Card>

            <!-- Carousel with Options -->
            <Card>
                <CardHeader>
                    <CardTitle>Carousel Options</CardTitle>
                    <CardDescription>Using Embla Carousel options for loop and alignment</CardDescription>
                </CardHeader>
                <CardContent class="space-y-8">
                    <div>
                        <p class="text-sm font-medium mb-3">Loop Enabled</p>
                        <Carousel :opts="{ loop: true }" class="w-full max-w-xs mx-auto">
                            <CarouselContent>
                                <CarouselItem v-for="i in 5" :key="i">
                                    <div class="p-1">
                                        <Card>
                                            <CardContent class="flex aspect-square items-center justify-center p-6">
                                                <span class="text-4xl font-semibold">{{ i }}</span>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious />
                            <CarouselNext />
                        </Carousel>
                    </div>
                    <div>
                        <p class="text-sm font-medium mb-3">Center Aligned</p>
                        <Carousel :opts="{ align: 'center' }" class="w-full max-w-md mx-auto">
                            <CarouselContent>
                                <CarouselItem v-for="i in 7" :key="i" class="basis-1/3">
                                    <div class="p-1">
                                        <Card>
                                            <CardContent class="flex aspect-square items-center justify-center p-4">
                                                <span class="text-2xl font-semibold">{{ i }}</span>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious />
                            <CarouselNext />
                        </Carousel>
                    </div>
                </CardContent>
            </Card>

            <!-- Carousel with API -->
            <Card>
                <CardHeader>
                    <CardTitle>Carousel with API</CardTitle>
                    <CardDescription>Access carousel state with the API</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col items-center gap-4">
                        <Carousel class="w-full max-w-xs" @init-api="setApi">
                            <CarouselContent>
                                <CarouselItem v-for="i in 5" :key="i">
                                    <div class="p-1">
                                        <Card>
                                            <CardContent class="flex aspect-square items-center justify-center p-6">
                                                <span class="text-4xl font-semibold">{{ i }}</span>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious />
                            <CarouselNext />
                        </Carousel>
                        <div class="py-2 text-center text-sm text-muted-foreground">
                            Slide {{ current }} of {{ count }}
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Carousel with Indicators -->
            <Card>
                <CardHeader>
                    <CardTitle>Carousel with Indicators</CardTitle>
                    <CardDescription>Dot indicators showing current slide position</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col items-center gap-4">
                        <Carousel class="w-full max-w-sm" @init-api="setIndicatorApi">
                            <CarouselContent>
                                <CarouselItem v-for="i in 5" :key="i">
                                    <div class="p-1">
                                        <Card>
                                            <CardContent class="flex aspect-video items-center justify-center p-6 bg-gradient-to-br from-primary/20 to-primary/5">
                                                <span class="text-4xl font-semibold">{{ i }}</span>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </CarouselContent>
                            <CarouselPrevious />
                            <CarouselNext />
                        </Carousel>
                        <div class="flex gap-2">
                            <button
                                v-for="(_, index) in 5"
                                :key="index"
                                :class="[
                                    'size-2 rounded-full transition-colors cursor-pointer',
                                    indicatorCurrent === index + 1
                                        ? 'bg-primary'
                                        : 'bg-muted-foreground/30 hover:bg-muted-foreground/50'
                                ]"
                                @click="indicatorApi?.scrollTo(index)"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Product Carousel -->
            <Card>
                <CardHeader>
                    <CardTitle>Product Carousel</CardTitle>
                    <CardDescription>Example use case for showcasing products</CardDescription>
                </CardHeader>
                <CardContent>
                    <Carousel :opts="{ align: 'start' }" class="w-full">
                        <CarouselContent class="-ml-2 md:-ml-4">
                            <CarouselItem v-for="product in products" :key="product.id" class="pl-2 md:pl-4 basis-full sm:basis-1/2 md:basis-1/3 lg:basis-1/4">
                                <Card class="h-full">
                                    <CardContent class="p-0">
                                        <div class="aspect-square bg-muted flex items-center justify-center">
                                            <Package class="size-12 text-muted-foreground" />
                                        </div>
                                        <div class="p-4">
                                            <h4 class="font-medium text-sm">{{ product.name }}</h4>
                                            <p class="text-sm text-muted-foreground">{{ product.category }}</p>
                                            <p class="mt-2 font-semibold">${{ product.price.toFixed(2) }}</p>
                                        </div>
                                    </CardContent>
                                </Card>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious class="-left-4" />
                        <CarouselNext class="-right-4" />
                    </Carousel>
                </CardContent>
            </Card>

            <!-- Testimonials Carousel -->
            <Card>
                <CardHeader>
                    <CardTitle>Testimonials Carousel</CardTitle>
                    <CardDescription>Auto-width cards with customer testimonials</CardDescription>
                </CardHeader>
                <CardContent>
                    <Carousel :opts="{ loop: true }" class="w-full max-w-2xl mx-auto">
                        <CarouselContent>
                            <CarouselItem v-for="testimonial in testimonials" :key="testimonial.id">
                                <div class="p-1">
                                    <Card>
                                        <CardContent class="p-6">
                                            <div class="flex items-start gap-4">
                                                <Avatar size="lg" shape="circle">
                                                    <AvatarFallback>{{ testimonial.initials }}</AvatarFallback>
                                                </Avatar>
                                                <div class="flex-1">
                                                    <p class="text-sm text-muted-foreground italic mb-4">
                                                        "{{ testimonial.quote }}"
                                                    </p>
                                                    <div>
                                                        <p class="font-medium text-sm">{{ testimonial.name }}</p>
                                                        <p class="text-xs text-muted-foreground">{{ testimonial.role }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious />
                        <CarouselNext />
                    </Carousel>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
    type CarouselApi,
} from '@/components/ui/carousel';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Package } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Carousel API example
const api = ref<CarouselApi>();
const current = ref(0);
const count = ref(0);

function setApi(val: CarouselApi) {
    api.value = val;
}

watchEffect(() => {
    if (!api.value) return;

    count.value = api.value.scrollSnapList().length;
    current.value = api.value.selectedScrollSnap() + 1;

    api.value.on('select', () => {
        current.value = api.value!.selectedScrollSnap() + 1;
    });
});

// Indicator carousel
const indicatorApi = ref<CarouselApi>();
const indicatorCurrent = ref(1);

function setIndicatorApi(val: CarouselApi) {
    indicatorApi.value = val;
}

watchEffect(() => {
    if (!indicatorApi.value) return;

    indicatorCurrent.value = indicatorApi.value.selectedScrollSnap() + 1;

    indicatorApi.value.on('select', () => {
        indicatorCurrent.value = indicatorApi.value!.selectedScrollSnap() + 1;
    });
});

// Sample data
const images = [
    { url: 'https://picsum.photos/seed/1/800/450', alt: 'Mountain landscape' },
    { url: 'https://picsum.photos/seed/2/800/450', alt: 'Ocean sunset' },
    { url: 'https://picsum.photos/seed/3/800/450', alt: 'Forest trail' },
    { url: 'https://picsum.photos/seed/4/800/450', alt: 'City skyline' },
    { url: 'https://picsum.photos/seed/5/800/450', alt: 'Desert dunes' },
];

const products = [
    { id: 1, name: 'Wireless Headphones', category: 'Electronics', price: 149.99 },
    { id: 2, name: 'Smart Watch', category: 'Electronics', price: 299.99 },
    { id: 3, name: 'Running Shoes', category: 'Sports', price: 89.99 },
    { id: 4, name: 'Backpack', category: 'Accessories', price: 59.99 },
    { id: 5, name: 'Water Bottle', category: 'Sports', price: 24.99 },
    { id: 6, name: 'Laptop Stand', category: 'Electronics', price: 79.99 },
    { id: 7, name: 'Desk Lamp', category: 'Home', price: 44.99 },
    { id: 8, name: 'Notebook Set', category: 'Office', price: 19.99 },
];

const testimonials = [
    {
        id: 1,
        name: 'Sarah Johnson',
        role: 'Product Manager at TechCorp',
        initials: 'SJ',
        quote: 'This product has completely transformed how our team works. The intuitive interface and powerful features have increased our productivity by 40%.',
    },
    {
        id: 2,
        name: 'Michael Chen',
        role: 'CEO at StartupXYZ',
        initials: 'MC',
        quote: 'I have tried many similar solutions, but nothing comes close to the quality and reliability we have experienced here. Highly recommended!',
    },
    {
        id: 3,
        name: 'Emily Rodriguez',
        role: 'Designer at CreativeStudio',
        initials: 'ER',
        quote: 'The attention to detail in the design is remarkable. It is clear that a lot of thought went into creating a seamless user experience.',
    },
];
</script>
