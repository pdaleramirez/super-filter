<template>
    <div class="combobox">
        <div class="wrap-input" v-on-clickaway="away">
            <input class="combobox-input" ref="search"
                   v-bind:placeholder="placeholder"
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
                v-on:click="select(option.value)"
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
                options: [
                    {value: 'table', label:'Table'},
                    {value:'list', label:'List'},
                    {value:'grid', label:'Grid'},
                    {value:'custom', label:'Add Custom'}
                ],
                showAutocompleteDropdown: false,
                selectedValue: null,
                inputModel: this.value,
                selectedIndex: 0,
                index: null,
                arrowDown: true
            }
        },
        props: {
            placeholder: String,
            value: String
        },
        mounted() {
            console.log('val prop');
            console.log(this.value);
        },
        methods: {
            away() {
                this.showAutocompleteDropdown = false;
            },
            handleBackspace() {
                this.showAutocompleteDropdown = true;
            },
            inputValue() {
                this.showAutocompleteDropdown = true;
            },
            select(value) {
                this.selectedValue = value;

                if (value === 'custom') {
                    this.inputModel = '';
                    this.readonly = false;
                    this.arrowDown = false;
                    this.$refs.search.focus();
                } else {
                    this.readonly = true;
                    this.arrowDown = true;
                    this.inputModel = value;
                }

                this.showAutocompleteDropdown = false;
            },
            closeInput() {
                console.log('close input here')
            },
            clearInput() {
                this.inputModel = '';
            },
            modelValue() {

            }
        },
        watch: {
            'inputModel': function (newVal, oldVal) {
                this.$emit("input", this.inputModel);
            }
        }
    };
</script>

<style lang="scss">
    @import 'css/combobox/main.scss';

</style>
