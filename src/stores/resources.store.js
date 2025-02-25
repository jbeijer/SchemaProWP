import { writable } from 'svelte/store';

export const resources = writable([]);
export const bookings = writable([]);
export const loading = writable({
    resources: false,
    bookings: false
});
export const errors = writable({
    resources: null,
    bookings: null
});

export const fetchResources = async (apiUrl, nonce) => {
    loading.update(l => ({ ...l, resources: true }));
    errors.update(e => ({ ...e, resources: null }));
    
    try {
        const response = await fetch(`${apiUrl}resources`, {
            headers: {
                'X-WP-Nonce': nonce
            }
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        resources.set(data);
    } catch (error) {
        console.error('Error fetching resources:', error);
        errors.update(e => ({ ...e, resources: error.message }));
    } finally {
        loading.update(l => ({ ...l, resources: false }));
    }
};

export const fetchBookings = async (apiUrl, nonce) => {
    loading.update(l => ({ ...l, bookings: true }));
    errors.update(e => ({ ...e, bookings: null }));
    
    try {
        const response = await fetch(`${apiUrl}bookings`, {
            headers: {
                'X-WP-Nonce': nonce
            }
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        bookings.set(data);
    } catch (error) {
        console.error('Error fetching bookings:', error);
        errors.update(e => ({ ...e, bookings: error.message }));
    } finally {
        loading.update(l => ({ ...l, bookings: false }));
    }
};
