export default {
    bind(el, binding, vnode) {
        const menuHandler = (e) => {
            if (!el.contains(e.target) && el !== e.target) {
                binding.value(e);
            }
        };

        el.outsideClick = menuHandler;
        document.addEventListener('mousedown', menuHandler);
    },
    unbind(el, binding) {
        document.removeEventListener('mousedown', el.outsideClick);
        el.outsideClick = null;
    },
};
