<script>
import { createResource } from '../../stores/resources.store.js';

export let wpData;

let organization = {
    type: 'Organization',
    name: '',
    description: '',
    websiteUrl: '',
    logoUrl: '',
    contactInfo: {
        email: '',
        phone: ''
    },
    location: {
        streetAddress: '',
        city: '',
        postalCode: '',
        country: ''
    }
};

let isLoading = false;
let errorMessage = '';
let successMessage = '';
let formErrors = {};

const validateForm = () => {
    const errors = {};
    if (!organization.name.trim()) {
        errors.name = 'Organization name is required';
    }
    if (!organization.websiteUrl.trim()) {
        errors.websiteUrl = 'Website URL is required';
    } else if (!/^https?:\/\//.test(organization.websiteUrl)) {
        errors.websiteUrl = 'Invalid URL format';
    }
    if (organization.contactInfo.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(organization.contactInfo.email)) {
        errors.email = 'Invalid email format';
    }
    return errors;
};

function handleInput(field) {
    formErrors[field] = '';
}

async function handleSubmit() {
    const validationErrors = validateForm();
    if (Object.keys(validationErrors).length > 0) {
        formErrors = validationErrors;
        return;
    }

    isLoading = true;
    errorMessage = '';
    successMessage = '';

    try {
        await createResource(wpData.apiUrl, wpData.nonce, organization);
        successMessage = 'Organization created successfully!';
        organization = {
            type: 'Organization',
            name: '',
            description: '',
            websiteUrl: '',
            logoUrl: '',
            contactInfo: {
                email: '',
                phone: ''
            },
            location: {
                streetAddress: '',
                city: '',
                postalCode: '',
                country: ''
            }
        };
        formErrors = {};
    } catch (error) {
        errorMessage = error.message || 'Failed to create organization';
    } finally {
        isLoading = false;
    }
}
</script>

<div class="create-organization">
    <h2>Create Organization</h2>
    <form on:submit|preventDefault={handleSubmit}>
        <div class="form-group">
            <label for="name">Organization Name *</label>
            <input 
                type="text" 
                id="name" 
                bind:value={organization.name} 
                on:input={() => handleInput('name')}
                required
                class={formErrors.name ? 'error-input' : ''}
            />
            {#if formErrors.name}
                <p class="error-message">{formErrors.name}</p>
            {/if}
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                id="description" 
                bind:value={organization.description}
            ></textarea>
        </div>

        <div class="form-group">
            <label for="websiteUrl">Website URL *</label>
            <input 
                type="url" 
                id="websiteUrl" 
                bind:value={organization.websiteUrl} 
                on:input={() => handleInput('websiteUrl')}
                required
                class={formErrors.websiteUrl ? 'error-input' : ''}
            />
            {#if formErrors.websiteUrl}
                <p class="error-message">{formErrors.websiteUrl}</p>
            {/if}
        </div>

        <div class="form-group">
            <label for="logoUrl">Logo URL</label>
            <input 
                type="url" 
                id="logoUrl" 
                bind:value={organization.logoUrl}
            />
        </div>

        <fieldset>
            <legend>Contact Information</legend>
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    bind:value={organization.contactInfo.email}
                    on:input={() => handleInput('email')}
                    class={formErrors.email ? 'error-input' : ''}
                />
                {#if formErrors.email}
                    <p class="error-message">{formErrors.email}</p>
                {/if}
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input 
                    type="tel" 
                    id="phone" 
                    bind:value={organization.contactInfo.phone}
                />
            </div>
        </fieldset>

        <fieldset>
            <legend>Location</legend>
            <div class="form-group">
                <label for="streetAddress">Street Address</label>
                <input 
                    type="text" 
                    id="streetAddress" 
                    bind:value={organization.location.streetAddress}
                />
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input 
                    type="text" 
                    id="city" 
                    bind:value={organization.location.city}
                />
            </div>

            <div class="form-group">
                <label for="postalCode">Postal Code</label>
                <input 
                    type="text" 
                    id="postalCode" 
                    bind:value={organization.location.postalCode}
                />
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <input 
                    type="text" 
                    id="country" 
                    bind:value={organization.location.country}
                />
            </div>
        </fieldset>

        {#if isLoading}
            <button type="submit" class="submit-button" disabled>
                <span>Loading...</span>
                <span class="loading-indicator"></span>
            </button>
        {:else}
            <button type="submit" class="submit-button">Create Organization</button>
        {/if}

        {#if errorMessage}
            <p style="color: red">{errorMessage}</p>
        {:else if successMessage}
            <p style="color: green">{successMessage}</p>
        {/if}
    </form>
</div>

<style>
    .create-organization {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #333;
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        color: #666;
        font-weight: 500;
    }

    input, textarea, select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.2s;
    }

    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .error-input {
        border-color: #dc3545;
    }

    .error-input:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
    }

    .loading-indicator {
        display: inline-block;
        margin-left: 0.5rem;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .submit-button {
        background: #007bff;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.2s;
    }

    .submit-button:hover {
        background: #0056b3;
    }
</style>
