<template>
    <div>
    <div class="row">
        <div class="col-3 fld-tab">
            <div class="tabs">
                <div class="tab draggable">
                    <span>Options</span>
                </div>
            </div>
            <draggable :swapThreshold="1" class="list-group fld-tabcontent" :list="options" :group="group">
                <div
                        class="fld-field"
                        v-for="(element, index) in options"
                        :key="element.name"
                >
                    {{ element.name }}
                </div>
            </draggable>
        </div>

        <div class="col-3 fld-tab">
            <div class="tabs">
                <div class="tab draggable">
                    <span>Selected</span>
                </div>
            </div>
            <draggable :swapThreshold="1" class="list-group fld-tabcontent" :list="selected" :group="group" @change="log">
                <div
                        class="fld-field"
                        v-for="(element, index) in selected"
                        :key="element.name"
                >
                    {{ element.name }}
                </div>
            </draggable>
        </div>
    </div>
    <div class="clearfix"></div>

    </div>
</template>
<script>
    import draggable from "vuedraggable";

    export default {
        name: "Cards",
        display: "Two Lists",
        order: 1,
        components: {
            draggable
        },
        props: {
            fields: {},
            selectedFields: {},
            group: null
        },
        data() {
            return {
                selected: [],
                options: []
            };
        },
        methods: {
            add: function() {
               console.log("add");
            },
            replace: function() {
                console.log("replace");
            },
            clone: function(el) {
                return {
                    name: el.name + " cloned"
                };
            },
            log: function(evt) {
                this.$emit('drag:fields', {
                    options: this.options,
                    selected: this.selected
                })
            }
        },
        mounted: function() {
            //this.options   =  Object.assign([], this.fields);
            this.options   = this.fields;
            this.selected  = this.selectedFields;

            // let url = "/admin/super-filter/setup-search/setup-options";
            // let data = {};
            //
            // data[csrfTokenName] = csrfTokenValue;
            // //this.sortOptions = this.sortOptions2;
            // axios.post(url, qs.stringify(data)).then((response) => {
            //
            //     this.options =  response.data['entry'].fields['superFilterShows'].options;
            // });

        },
        watch: {
            fields(value) {
                //this.options  = value;
               // this.selected  = value.selected;
            },
            selectedFields(value) {
               // this.selected  = value;
            }
        }
    };
</script>
<style scoped>

    .clearfix {
        clear: both
    }

    .side {
        border: 1px solid black;
        width: 300px;
        float: left;
        padding: 10px;
    }

    .list-group-item {
        border: 1px solid black;
        padding: 10px;
        margin: 10px 0;
    }

    .sortable-ghost {
        border: 1px dashed gray;
    }

    .sortable-chosen {
        border: 1px dashed blue;
    }
</style>
