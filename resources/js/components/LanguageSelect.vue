<template>
    <div
        v-bind:class="{ 'rounded-t-md': isOpen, 'rounded-md': !isOpen }"
        class="relative w-full max-w-xs mb-8 bg-gray-100 cursor-pointer sm:w-5/12 sm:mb-0"
    >
        <label class="sr-only">Language selector</label>
        <button
            @click="isOpen = !isOpen"
            ref="button"
            for="language selection"
            class="flex items-center justify-between w-full px-3 py-2"
        >
            <p>
                {{ selected }}
            </p>
            <svg
                v-bind:class="{ 'transform rotate-180': isOpen }"
                class="transition"
                width="20"
                height="24"
                xmlns="http:www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>

        <ul
            v-show="isOpen"
            v-outside-click="{
                ignore: ['button'],
                handleClose: 'hide',
            }"
            class="absolute left-0 w-full bg-white shadow-md rounded-b-md top-10"
        >
            <li
                v-for="(language, index) in languages"
                :key="index"
                class="transition rounded-b-md hover:bg-gray-200"
            >
                <a
                    href="#"
                    class="block"
                    @click.prevent="handleSelect(language)"
                >
                    <p class="px-3 py-2">
                        {{ language.language_name_native }}
                    </p>
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
    props: ['languages', 'currentLanguage'],

    setup(props) {
        const isOpen = ref(false);
        const selected = ref(null);

        onMounted(() => {
            selected.value = props.currentLanguage;
        });

        const handleSelect = (languageObject) => {
            selected.value = languageObject.language_name_native;
            window.location.href = languageObject.language_url;
            isOpen.value = false;
        };

        const hide = () => {
            isOpen.value = false;
        };

        return {
            isOpen,
            selected,
            handleSelect,
            hide,
        };
    },
};
</script>
