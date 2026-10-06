import { useToast } from 'vue-toast-notification'

const toast = useToast({
    position: 'top-right',
    duration: 3000,
})

export function useAppToast() {
    return toast
}
