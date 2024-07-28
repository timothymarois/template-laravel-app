const state = reactive({
    isTop: true
});

const setTop = (top) => {
    state.isTop = top;
};

export default function usePageTop() {
    return {
        ...toRefs(state),
        setTop
    };
}
