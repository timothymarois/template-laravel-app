<template>
    <main ref="pagemain" class="page-body-h overflow-y-auto">
        <div class="mx-auto max-w-screen-2xl px-4 py-6 sm:px-6">
            <slot />
        </div>
    </main>
</template>

<script setup>
const { setTop } = usePageTop();
const pagemain = ref(null)

const dynamicHeight = ref('0px');

const updateHeight = () => {
    const offsets = calculateOffsets();
    dynamicHeight.value = offsets + 'px';
};

const calculateOffsets = () => {
    const pageBodyElement = document.querySelector('.page-body-h');
    let totalOffset = 0;
    if (pageBodyElement) {

        console.log(pageBodyElement.parentElement.children)
        const siblings = [...pageBodyElement.parentElement.children];
        siblings.forEach(sibling => {
            if (sibling !== pageBodyElement) {
                totalOffset += sibling.offsetHeight;
            }
        });
    }

    console.log(totalOffset)
    return totalOffset;
};

onBeforeUnmount(() => {
    setTop(true)
    pagemain.value.removeEventListener('scroll', handleScroll);
});

onMounted(() => {
    setTop(true)
    pagemain.value.addEventListener('scroll', handleScroll);
    nextTick(() => {
        updateHeight();
    })
});

watch(dynamicHeight, (newValue) => {
    document.documentElement.style.setProperty('--dynamic-height', newValue);
});

const handleScroll = () => {
	if(pagemain.value.scrollTop > 1) {
		setTop(false)
	} else {
		setTop(true)
	}
}
</script>
