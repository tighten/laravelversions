export default {
    bind(el, binding, vnode) {
        const outsideClickHandler = (e) => {
            const { handler, ignore } = binding.value;
            let clickedOnIgnoredEl = false;

            ignore.forEach((refName) => {
                if (!clickedOnIgnoredEl) {
                    const ignoredEl = vnode.context.$refs[refName];
                    clickedOnIgnoredEl = ignoredEl.contains(e.target);
                }
            });

            if (!el.contains(e.target) && !clickedOnIgnoredEl) {
                vnode.context[handler]();
            }
        };

        document.addEventListener('click', outsideClickHandler);
        document.addEventListener('touchstart', outsideClickHandler);
    },

    unbind() {
        document.removeEventListener('click', outsideClickHandler);
        document.removeEventListener('touchstart', outsideClickHandler);
    },
};
