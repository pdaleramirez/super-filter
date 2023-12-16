import {useEntriesStore} from "../stores/entries";
import {storeToRefs} from "pinia";
import {computed} from "vue";

export default function useField(handle = '') {

    const store = useEntriesStore();

    const { searchFieldsInfo } = storeToRefs(store);

    const SearchField = computed(() => {
        return searchFieldsInfo.value[handle];
    });
console.log('SearchField')
console.log(SearchField)
    return {
        SearchField
    };
}