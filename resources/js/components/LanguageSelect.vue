<template>
    <div>
        <div @click="isOpen = !isOpen">
            {{ selected }}
        </div>

        <ul v-show="isOpen" class="bg-white">
            <li
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
