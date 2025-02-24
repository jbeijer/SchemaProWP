import { writable } from 'svelte/store';

// Create stores with default values
export const appState = writable({
    isAdmin: false,
    wpData: {}
});

// Initialize store with data from DOM and window
export function initializeStores() {
    const element = document.getElementById('schemaprowp-app');
    const isAdmin = element?.dataset?.isAdmin === '1';
    const wpData = window.schemaProWPData || {};
    
    appState.set({
        isAdmin,
        wpData
    });
    
    return { isAdmin, wpData };
}
