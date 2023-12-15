import {ref} from "vue";

export default function filter(fn) {

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

    const submit = async (...params) => {

        let handle = params[0];
        let mutation = params[1];
        let fieldEntries = Object.entries(params[2]);

        let delayTimer;
        for (const [key, field] of fieldEntries) {

            if (mutation.events.target.handle === key) {
                clearTimeout(delayTimer);
                delayTimer = setTimeout(() => {

                    fn(handle, fieldEntries);
                }, 500);
            }
        }

       //  await fn(...params);
    }

    return {get, loading, error, submit};
}