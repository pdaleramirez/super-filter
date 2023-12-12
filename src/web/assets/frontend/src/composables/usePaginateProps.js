// usePaginateProps.js
export function usePaginateProps() {
    return {
        'max-pages-shown': {
            type: Number,
            default: 1
        },
        'dir': {
            type: String,
            default: 'ltr'
        },
        'type': {
            type: String,
            default: 'default'
        },
        'locale': {
            type: String,
            default: 'en'
        },
        'prev-button-content': {
            type: String,
            default: 'Prev'
        },
        'next-button-content': {
            type: String,
            default: 'Next'
        },
        'hide-prev-next': {
            type: Boolean,
            default: false
        },
        'hide-prev-next-when-ends': {
            type: Boolean,
            default: false
        },
        'show-breakpoint-buttons': {
            type: Boolean,
            default: false
        },
        'disable-breakpoint-buttons': {
            type: Boolean,
            default: false
        },
        'starting-breakpoint-content': {
            type: String,
            default: '...'
        },
        'ending-breakpoint-button-content': {
            type: String,
            default: '...'
        },
        'show-jump-buttons': {
            type: Boolean,
            default: false
        },
        'link-url': {
            type: String,
            default: '#'
        },
        'backward-jump-button-content': {
            type: String,
            default: '<<'
        },
        'forward-jump-button-content': {
            type: String,
            default: '>>'
        },
        'disable-pagination': {
            type: Boolean,
            default: false
        },
        'show-ending-buttons': {
            type: Boolean,
            default: false
        },
        'first-page-content': {
            type: String,
            default: 'First'
        },
        'last-page-content': {
            type: String,
            default: 'Last'
        },
        // ... add the rest of the props here
    }
}