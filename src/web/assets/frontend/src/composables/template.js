import {ref} from "vue";

export default function template(fn) {

    const loading = ref(false)
    const error = ref(null)

    const get = async (handle) => {
        try {
            loading.value = true;

            await fn(handle);

        } catch (err) {
            loading.value = false;
            error.value = err;
        } finally {
            loading.value = false;
        }

    }

    return {get, loading, error};
}