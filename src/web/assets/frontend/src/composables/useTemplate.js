import {ref} from "vue";

export default function useTemplate(fn) {

    const loading = ref(false)
    const error = ref(null)

    const get = async (...params) => {
        try {
            loading.value = true;

            await fn(...params);

        } catch (err) {
            loading.value = false;
            error.value = err;
        } finally {
            loading.value = false;
        }

    }

    return {get, loading, error};
}