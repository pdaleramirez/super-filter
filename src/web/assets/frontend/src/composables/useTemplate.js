import {ref} from "vue";
import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";

export default function useTemplate(fn) {

    const loading = ref(false)
    const error = ref(null)
    const store = useEntriesStore(); // Use the store
    const get = async (...params) => {
        try {
            loading.value = true;

            await fn(...params);

            const { templates } = storeToRefs(store);

            return templates.value[params[1]];
        } catch (err) {
            loading.value = false;
            error.value = err;
        } finally {
            loading.value = false;
        }

    }

    return {get, loading, error};
}