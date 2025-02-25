<script>
import { onMount } from 'svelte';
import { organizations } from '../../stores/organizations.store.js';

export let wpData;
export let value;
export let required = false;

onMount(() => {
    organizations.fetchOrganizations(wpData);
});
</script>

<div class="form-group">
    <label for="organization">Organization {required ? '*' : ''}</label>
    <select 
        id="organization"
        bind:value={value}
        {required}
    >
        <option value="">Select an organization</option>
        {#if $organizations.loading}
            <option disabled>Loading organizations...</option>
        {:else if $organizations.error}
            <option disabled>Error: {$organizations.error}</option>
        {:else}
            {#each $organizations.items as org}
                <option value={org.id}>{org.name}</option>
            {/each}
        {/if}
    </select>
</div>

<style>
.form-group {
    margin-bottom: 1rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
}

select:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 0 1px #007cba;
}
</style>
