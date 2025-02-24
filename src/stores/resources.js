import { writable } from 'svelte/store';
import apiFetch from '@wordpress/api-fetch';

function createResourcesStore() {
    const { subscribe, set, update } = writable({
        items: [],
        loading: false,
        error: null
    });

    return {
        subscribe,
        fetchResources: async () => {
            update(state => ({ ...state, loading: true, error: null }));
            try {
                const response = await apiFetch({
                    path: '/wp/v2/schemapro/resources'
                });
                update(state => ({
                    ...state,
                    items: response,
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
        addResource: async (resource) => {
            update(state => ({ ...state, loading: true, error: null }));
            try {
                const response = await apiFetch({
                    path: '/wp/v2/schemapro/resources',
                    method: 'POST',
                    data: resource
                });
                update(state => ({
                    ...state,
                    items: [...state.items, response],
                    loading: false
                }));
                return response;
            } catch (error) {
                update(state => ({
                    ...state,
                    error: error.message,
                    loading: false
                }));
                throw error;
            }
        },
        updateResource: async (id, resource) => {
            update(state => ({ ...state, loading: true, error: null }));
            try {
                const response = await apiFetch({
                    path: `/wp/v2/schemapro/resources/${id}`,
                    method: 'PUT',
                    data: resource
                });
                update(state => ({
                    ...state,
                    items: state.items.map(item => 
                        item.id === id ? response : item
                    ),
                    loading: false
                }));
                return response;
            } catch (error) {
                update(state => ({
                    ...state,
                    error: error.message,
                    loading: false
                }));
                throw error;
            }
        },
        deleteResource: async (id) => {
            update(state => ({ ...state, loading: true, error: null }));
            try {
                await apiFetch({
                    path: `/wp/v2/schemapro/resources/${id}`,
                    method: 'DELETE'
                });
                update(state => ({
                    ...state,
                    items: state.items.filter(item => item.id !== id),
                    loading: false
                }));
            } catch (error) {
                update(state => ({
                    ...state,
                    error: error.message,
                    loading: false
                }));
                throw error;
            }
        }
    };
}

export const resourcesStore = createResourcesStore();
