<script>
import { onMount } from 'svelte';
import { resourcesStore } from '../stores/resources';
import ResourceForm from './ResourceForm.svelte';

let showForm = false;
let editingResource = null;

onMount(() => {
    resourcesStore.fetchResources();
});

function handleEdit(resource) {
    editingResource = resource;
    showForm = true;
}

async function handleDelete(id) {
    if (confirm('Är du säker på att du vill ta bort denna resurs?')) {
        try {
            await resourcesStore.deleteResource(id);
        } catch (error) {
            console.error('Failed to delete resource:', error);
        }
    }
}

function handleFormClose() {
    showForm = false;
    editingResource = null;
}

function handleFormSubmit() {
    showForm = false;
    editingResource = null;
    resourcesStore.fetchResources();
}
</script>

<div class="resources-list">
    <div class="resources-header">
        <h2>Resurser</h2>
        <button class="button button-primary" on:click={() => { showForm = true; editingResource = null; }}>
            Lägg till ny resurs
        </button>
    </div>

    {#if $resourcesStore.loading}
        <div class="loading">Laddar resurser...</div>
    {:else if $resourcesStore.error}
        <div class="error">Ett fel uppstod: {$resourcesStore.error}</div>
    {:else if $resourcesStore.items.length === 0}
        <div class="empty">Inga resurser hittades.</div>
    {:else}
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Typ</th>
                    <th>Status</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody>
                {#each $resourcesStore.items as resource (resource.id)}
                    <tr>
                        <td>{resource.title}</td>
                        <td>{resource.type}</td>
                        <td>{resource.status}</td>
                        <td class="actions">
                            <button class="button" on:click={() => handleEdit(resource)}>
                                Redigera
                            </button>
                            <button class="button button-link-delete" on:click={() => handleDelete(resource.id)}>
                                Ta bort
                            </button>
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    {/if}

    {#if showForm}
        <ResourceForm 
            resource={editingResource} 
            on:close={handleFormClose}
            on:submit={handleFormSubmit}
        />
    {/if}
</div>

<style>
.resources-list {
    margin: 20px 0;
}

.resources-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.loading, .error, .empty {
    padding: 20px;
    text-align: center;
}

.error {
    color: #d63638;
}

.actions {
    display: flex;
    gap: 10px;
}

.button-link-delete {
    color: #d63638;
}

.button-link-delete:hover {
    color: #d63638;
    text-decoration: underline;
}
</style>
