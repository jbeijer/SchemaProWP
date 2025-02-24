import { writable } from 'svelte/store';

export const bookings = writable([]);
export const selectedBooking = writable(null);

export const fetchBookings = async (resourceId, startTime, endTime) => {
    try {
        const params = new URLSearchParams({
            resource_id: resourceId,
            start_time: startTime,
            end_time: endTime
        });

        const response = await fetch(`${window.schemaProWPData.restUrl}/bookings?${params}`, {
            headers: {
                'X-WP-Nonce': window.schemaProWPData.nonce
            }
        });
        const data = await response.json();
        bookings.set(data);
    } catch (error) {
        console.error('Error fetching bookings:', error);
    }
};
