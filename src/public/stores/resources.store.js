import { writable } from 'svelte/store';

export const resources = writable([]);
export const selectedResource = writable(null);

export const fetchResources = async () => {
    try {
        const response = await fetch(`${window.schemaProWPData.restUrl}/resources`, {
            headers: {
                'X-WP-Nonce': window.schemaProWPData.nonce
            }
        });
        const data = await response.json();
        resources.set(data);
    } catch (error) {
        console.error('Error fetching resources:', error);
    }
};
