<template>
    <div class="combobox">
        <div class="wrap-input" v-on-clickaway="away">
            <input class="combobox-input" ref="search"
                   v-model="inputModel"
                   v-on:click="inputValue"
                   :readonly="readonly"
                   @focusout="closeInput"
            /> <div v-if="inputModel !== '' && selectedValue === 'custom'" @click="clearInput" class="close"></div>
            <span v-on:click="inputValue" :class="{'classic': arrowDown, 'focus': showAutocompleteDropdown}"></span>
            <div class="clear"></div>
        </div>
        <ul class="combobox-list" v-if="showAutocompleteDropdown">
            <li class="combobox-list-item"
                v-for="(option, index) in options"
                v-on:click="select(option)"
                v-bind:class="{'selected': option.value === selectedValue, 'add-custom': option.value === 'custom'}">
                {{ option.label }}
            </li>
        </ul>
    </div>
</template>

<script>
    import { mixin as clickaway } from 'vue-clickaway';

    export default {
        name: 'ComboBox',
        mixins: [ clickaway ],
        data() {
            return {
                time: "",
                readonly: true,
                showAutocompleteDropdown: false,
                selectedValue: null,
                inputModel: this.value,
                selectedIndex: 0,
                index: null,
                arrowDown: true
            }
        },
        props: {
            options: Array,
            value: String
        },
        mounted() {
            if (this.value !== '') {
                this.inputModel = this.getLabel(this.value);
            }
        },
        methods: {
            away() {
                this.showAutocompleteDropdown = false;
            },
            getLabel(value) {
                let obj = this.options.find(obj => { return obj.value === value });

                if (obj) {
                    return obj.label;
                } else {
                    this.selectedValue = 'custom';
                    this.arrowDown = false;
                    this.readonly = false;
                    return value;
                }
            },
            handleBackspace() {
                this.showAutocompleteDropdown = true;
            },
            inputValue() {
                this.showAutocompleteDropdown = true;
            },
            select(option) {

                this.selectedValue = option.value;

                if (this.selectedValue === 'custom') {

                    let isType = this.options.find(obj => { return obj.label === this.inputModel });
                    if (isType) {
                        this.inputModel = '';
                    }

                    this.readonly = false;
                    this.arrowDown = false;
                    this.$refs.search.focus();
                } else {
                    this.readonly = true;
                    this.arrowDown = true;
                    this.inputModel = option.label;
                }

                this.showAutocompleteDropdown = false;
            },
            closeInput() {

            },
            clearInput() {
                this.inputModel = '';
            },
            modelValue() {

            }
        },
        watch: {
            'selectedValue': function (newVal, oldVal) {
                if (this.selectedValue !== 'custom') {
                    this.$emit("input", this.selectedValue);
                }
            },
            'inputModel': function (newVal, oldVal) {
                if (this.selectedValue === 'custom') {
                    this.$emit("input", this.inputModel);
                }
            }
        }
    };
</script>

<style lang="scss">
    @import 'css/combobox/main.scss';

</style>
