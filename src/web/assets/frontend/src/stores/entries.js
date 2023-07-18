import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios';

export const useEntriesStore = defineStore('entries', () => {
    const entries = ref([]);
    const data = { handle: 'superFilterShows' };
    async function getEntries() {
        const response = await axios.post( '/api/super-filter/fields', data);

        return response.data;
    }

    return { entries, getEntries }
})
