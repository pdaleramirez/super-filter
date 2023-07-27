export function useUrl(url) {
    const getUrl = (url) => {
        let prefix = '';
        if (import.meta.env.MODE === 'development') {
            prefix = import.meta.env.VITE_PROXY
        }

        return prefix + '/' + url;
    }

    return { getUrl };
}