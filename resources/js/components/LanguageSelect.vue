<template>
    <div
        class="relative w-full max-w-xs px-3 py-1 mb-8 bg-white cursor-pointer sm:py-2 sm:w-1/4 sm:mb-0"
    >
        <div
            @click="isOpen = !isOpen"
            class="flex items-center justify-between"
        >
            {{ selected }}
            <svg
                v-bind:class="{
                    'transform rotate-180 transition': isOpen,
                    transition: !isOpen,
                }"
                width="20"
                height="24"
                xmlns="http://www.w3.org/2000/svg"
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
        </div>
        <ul
            v-show="isOpen"
            class="absolute left-0 w-full bg-white shadow-md top-8"
        >
            <li
                class="px-3 py-2 transition hover:bg-gray-200"
                v-for="(language, index) in languages"
                :key="index"
                @click="handleSelect(language)"
            >
                {{ language.language_name_native }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        languages: null,
        currentLanguage: '',
    },
    mounted() {
        this.selected = this.currentLanguage;
    },
    data() {
        return {
            isOpen: false,
            selected: null,
        };
    },
    methods: {
        handleSelect(languageObject) {
            this.selected = languageObject.language_name_native;
            window.location.href = languageObject.language_url;
            return (this.isOpen = false);
        },
    },
};
</script>
