<script>
import { onMount } from 'svelte';
import OrganizationSelect from './OrganizationSelect.svelte';

export let wpData;

let resource = {
    name: '',
    description: '',
    type: 'equipment',
    status: 'active',
    organization_id: ''
};

let isSubmitting = false;
let errorMessage = '';
let successMessage = '';

async function handleSubmit() {
    isSubmitting = true;
    errorMessage = '';
    successMessage = '';

    // Validate organization
    if (!resource.organization_id) {
        errorMessage = 'Organization is required';
        isSubmitting = false;
        return;
    }

    try {
        const response = await fetch(`${wpData.apiUrl}/resources`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': wpData.nonce
            },
            body: JSON.stringify(resource)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to create resource');
        }

        const data = await response.json();
        successMessage = 'Resource created successfully!';
        resource = {
            name: '',
            description: '',
            type: 'equipment',
            status: 'active',
            organization_id: ''
        };
    } catch (error) {
        console.error('Error creating resource:', error);
        errorMessage = error.message || 'An error occurred while creating the resource';
    } finally {
        isSubmitting = false;
    }
}
</script>

<div class="create-resource">
    <h2>Create New Resource</h2>
    
    {#if errorMessage}
        <div class="error-message">{errorMessage}</div>
    {/if}
    
    {#if successMessage}
        <div class="success-message">{successMessage}</div>
    {/if}

    <form on:submit|preventDefault={handleSubmit}>
        <OrganizationSelect 
            {wpData}
            bind:value={resource.organization_id}
            required={true}
        />

        <div class="form-group">
            <label for="name">Name *</label>
            <input 
                type="text" 
                id="name"
                bind:value={resource.name}
                required
                placeholder="Enter resource name"
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                id="description"
                bind:value={resource.description}
                placeholder="Enter resource description"
                rows="4"
            ></textarea>
        </div>

        <div class="form-group">
            <label for="type">Type *</label>
            <select 
                id="type"
                bind:value={resource.type}
                required
            >
                <option value="room">Room</option>
                <option value="equipment">Equipment</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select 
                id="status"
                bind:value={resource.status}
            >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <button type="submit" disabled={isSubmitting}>
            {isSubmitting ? 'Creating...' : 'Create Resource'}
        </button>
    </form>
</div>

<style>
    .create-resource {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input, textarea, select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    button {
        background-color: #2271b1;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .error-message {
        color: #dc3232;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #dc3232;
        border-radius: 4px;
        background-color: #fcf0f0;
    }

    .success-message {
        color: #46b450;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #46b450;
        border-radius: 4px;
        background-color: #f0f8f0;
    }
</style>
