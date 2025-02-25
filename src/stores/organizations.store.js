import { writable } from 'svelte/store';

function createOrganizationsStore() {
    const { subscribe, set, update } = writable({
        items: [],
        loading: false,
        error: null
    });

    return {
        subscribe,
        fetchOrganizations: async (wpData) => {
            update(state => ({ ...state, loading: true, error: null }));
            try {
                const response = await fetch(`${wpData.apiUrl}/organizations`, {
                    headers: {
                        'X-WP-Nonce': wpData.nonce
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch organizations');
                }

                const data = await response.json();
                update(state => ({
                    ...state,
                    items: data,
                    loading: false
                }));
            } catch (error) {
                update(state => ({
                    ...state,
                    error: error.message,
                    loading: false
                }));
            }
        },
        reset: () => {
            set({ items: [], loading: false, error: null });
        }
    };
}

export const organizations = createOrganizationsStore();
