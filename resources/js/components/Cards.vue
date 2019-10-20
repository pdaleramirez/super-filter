<template>
    <div>
    <div class="row">
        <div class="col-3 fld-tab">
            <div class="tabs">
                <div class="tab draggable">
                    <span>Tab 1</span>
                </div>
            </div>
            <draggable :swapThreshold="1" class="list-group fld-tabcontent" :list="list1" group="people" @change="log">
                <div
                        class="fld-field"
                        v-for="(element, index) in list1"
                        :key="element.name"
                >
                    {{ element.name }} {{ index }}
                </div>
            </draggable>
        </div>

        <div class="col-3 fld-tab">
            <div class="tabs">
                <div class="tab draggable">
                    <span>Tab 2</span>
                </div>
            </div>
            <draggable :swapThreshold="1" class="list-group fld-tabcontent" :list="list2" group="people" @change="log">
                <div
                        class="fld-field"
                        v-for="(element, index) in list2"
                        :key="element.name"
                >
                    {{ element.name }} {{ index }}
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
        data() {
            return {
                list1: [
                    { name: "John", id: 1 },
                    { name: "Joao", id: 2 },
                    { name: "Jean", id: 3 },
                    { name: "Gerard", id: 4 }
                ],
                list2: [
                    { name: "Juan", id: 5 },
                    { name: "Edgard", id: 6 },
                    { name: "Johnson", id: 7 }
                ]
            };
        },
        methods: {
            add: function() {
                this.list.push({ name: "Juan" });
            },
            replace: function() {
                this.list = [{ name: "Edgard" }];
            },
            clone: function(el) {
                return {
                    name: el.name + " cloned"
                };
            },
            log: function(evt) {
                window.console.log(evt);
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
