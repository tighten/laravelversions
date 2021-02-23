<template>
    <div>
        // this is before you click it
        <div @click="isOpen = !isOpen">
            {{ selected }}
        </div>

        // when open, display options
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
    // nothing is selected yet
    props: {
        languages: null,
        currentLanguage: '',
    },

    // on page load, checks props to make sure you see the right shit
    mounted() {
        this.selected = this.currentLanguage;
    },

    // state managemnt (open or no? selected or no?)
    data() {
        return {
            isOpen: false,
            selected: null,
        };
    },

    // the actual functionality of components/updating selection
    methods: {
        handleSelect(languageObject) {
            this.selected = languageObject.language_name_native;
            window.location.href = languageObject.language_url;
            return (this.isOpen = false);
        },
    },
};
</script>
