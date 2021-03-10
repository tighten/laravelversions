export default {
    bind(el, binding, vnode) {
        const outsideClickHandler = (e) => {
            const { handleClose, ignore } = binding.value;
            let clickedIgnoredElement = false;

            ignore.forEach((refName) => {
                if (!clickedIgnoredElement) {
                    const ignoredElement = vnode.context.$refs[refName];
                    clickedIgnoredElement = ignoredElement.contains(e.target);
                }
            });
            if (!el.contains(e.target) && !clickedIgnoredElement) {
                vnode.context[handleClose]();
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
