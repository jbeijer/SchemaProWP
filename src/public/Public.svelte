<script>
import { onMount } from 'svelte';
import { resources, loading, errors, fetchResources } from '../stores/resources.store.js';
import CreateResource from './components/CreateResource.svelte';
import CreateOrganization from './components/CreateOrganization.svelte';

export let wpData;

onMount(() => {
    fetchResources(wpData.apiUrl, wpData.nonce);
});
</script>

<div class="schema-pro-wp-public">
    <!-- Resources Section -->
    <section class="resources-section">
        <h2>Resources</h2>
        
        {#if $loading.resources}
            <div class="loading">Loading resources...</div>
        {:else if $errors.resources}
            <div class="error">
                <p>Error loading resources: {$errors.resources}</p>
                <button on:click={() => fetchResources(wpData.apiUrl, wpData.nonce)}>
                    Retry
                </button>
            </div>
        {:else}
            <div class="resources-grid">
                {#each $resources as resource}
                    <div class="resource-card">
                        <h3>{resource.title}</h3>
                        <p>{resource.description}</p>
                        <div class="resource-meta">
                            <span class="type">{resource.type}</span>
                            <span class="status">{resource.status}</span>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </section>
    <CreateResource {wpData} />
    <CreateOrganization {wpData} />
</div>

<style>
.schema-pro-wp-public {
    padding: 2rem;
}

.resources-section {
    max-width: 1200px;
    margin: 0 auto;
}

h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: #333;
}

.loading {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.error {
    background-color: #fee;
    border: 1px solid #fcc;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.error button {
    background-color: #f44;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 0.5rem;
}

.error button:hover {
    background-color: #e33;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.resource-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
    transition: transform 0.2s ease;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.resource-card h3 {
    margin: 0 0 1rem;
    color: #333;
    font-size: 1.25rem;
}

.resource-card p {
    color: #666;
    margin: 0 0 1rem;
    line-height: 1.5;
}

.resource-meta {
    display: flex;
    gap: 1rem;
    margin-top: auto;
}

.resource-meta span {
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.875rem;
}

.type {
    background-color: #e3f2fd;
    color: #1976d2;
}

.status {
    background-color: #e8f5e9;
    color: #2e7d32;
}
</style>