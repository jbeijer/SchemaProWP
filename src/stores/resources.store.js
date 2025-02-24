import { writable } from 'svelte/store';

// Create stores
export const resources = writable([]);
export const loading = writable({
    resources: false,
});
export const errors = writable({
    resources: null,
});

// Fetch resources from the API
export const fetchResources = async (apiUrl, nonce, post_id) => {
    if (!post_id) {
        errors.update(e => ({ ...e, resources: 'Missing required parameter: post_id' }));
        return;
    }

    loading.update(l => ({ ...l, resources: true }));
    errors.update(e => ({ ...e, resources: null }));
    
    try {
        const response = await fetch(`${apiUrl}/resources?post_id=${post_id}`, {
            headers: {
                'X-WP-Nonce': nonce,
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        resources.set(data.items || []);
    } catch (error) {
        console.error('Error fetching resources:', error);
        errors.update(e => ({ ...e, resources: error.message }));
    } finally {
        loading.update(l => ({ ...l, resources: false }));
    }
};

// Create a new resource
export const createResource = async (apiUrl, nonce, resourceData) => {
    // Check if post_id exists
    if (!resourceData.post_id) {
        console.warn('Attempting to create resource without post_id, using default');
        resourceData.post_id = 0; // Use 0 or a default value that works in your system
    }

    // Validate resource type
    const validTypes = ['room', 'equipment'];
    if (!resourceData.type || !validTypes.includes(resourceData.type)) {
        console.warn('Invalid or missing resource type, defaulting to room');
        resourceData.type = 'room';
    }

    loading.update(l => ({ ...l, resources: true }));
    errors.update(e => ({ ...e, resources: null }));
    
    try {
        const response = await fetch(`${apiUrl}/resources`, {
            method: 'POST',
            headers: {
                'X-WP-Nonce': nonce,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(resourceData)
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        const newResource = await response.json();
        resources.update(current => [...current, newResource]);
        return newResource;
    } catch (error) {
        console.error('Error creating resource:', error);
        errors.update(e => ({ ...e, resources: error.message }));
        throw error;
    } finally {
        loading.update(l => ({ ...l, resources: false }));
    }
};

// Update an existing resource
export const updateResource = async (apiUrl, nonce, resourceId, resourceData) => {
    loading.update(l => ({ ...l, resources: true }));
    errors.update(e => ({ ...e, resources: null }));
    
    try {
        const response = await fetch(`${apiUrl}/resources/${resourceId}`, {
            method: 'PUT',
            headers: {
                'X-WP-Nonce': nonce,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(resourceData)
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        const updatedResource = await response.json();
        resources.update(current => 
            current.map(resource => 
                resource.id === resourceId ? updatedResource : resource
            )
        );
        return updatedResource;
    } catch (error) {
        console.error('Error updating resource:', error);
        errors.update(e => ({ ...e, resources: error.message }));
        throw error;
    } finally {
        loading.update(l => ({ ...l, resources: false }));
    }
};

// Delete a resource
export const deleteResource = async (apiUrl, nonce, resourceId) => {
    loading.update(l => ({ ...l, resources: true }));
    errors.update(e => ({ ...e, resources: null }));
    
    try {
        const response = await fetch(`${apiUrl}/resources/${resourceId}`, {
            method: 'DELETE',
            headers: {
                'X-WP-Nonce': nonce,
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        resources.update(current => 
            current.filter(resource => resource.id !== resourceId)
        );
    } catch (error) {
        console.error('Error deleting resource:', error);
        errors.update(e => ({ ...e, resources: error.message }));
        throw error;
    } finally {
        loading.update(l => ({ ...l, resources: false }));
    }
};
