export default {
    mounted(el, binding) {
        el.clickOutsideEvent = (e) => {
            const { handleClose, ignore } = binding.value;
            let clickedIgnoredElement = false;

            ignore.forEach((refName) => {
                if (!clickedIgnoredElement) {
                    const ignoredElement = binding.instance.$refs[refName];
                    clickedIgnoredElement = ignoredElement.contains(e.target);
                }
            });

            if (!el.contains(e.target) && !clickedIgnoredElement) {
                binding.instance[handleClose]();
            }
        };

        document.addEventListener('click', el.clickOutsideEvent);
        document.addEventListener('touchstart', el.clickOutsideEvent);
    },
    unmounted: function (el) {
        document.removeEventListener('click', el.el.clickOutsideEvent);
        document.removeEventListener('touchstart', el.clickOutsideEvent);
    },
};
