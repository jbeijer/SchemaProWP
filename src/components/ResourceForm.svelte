<script>
import { createEventDispatcher } from 'svelte';
import { resourcesStore } from '../stores/resources';

export let resource = null;

const dispatch = createEventDispatcher();

let title = resource?.title || '';
let description = resource?.description || '';
let type = resource?.type || 'room';
let status = resource?.status || 'active';
let submitting = false;
let error = null;

const resourceTypes = [
    { value: 'room', label: 'Rum' },
    { value: 'equipment', label: 'Utrustning' },
    { value: 'vehicle', label: 'Fordon' },
    { value: 'other', label: 'Övrigt' }
];

const statusTypes = [
    { value: 'active', label: 'Aktiv' },
    { value: 'inactive', label: 'Inaktiv' },
    { value: 'maintenance', label: 'Under underhåll' }
];

async function handleSubmit() {
    if (!title.trim()) {
        error = 'Titel är obligatoriskt';
        return;
    }

    submitting = true;
    error = null;

    try {
        const resourceData = {
            title,
            description,
            type,
            status
        };

        if (resource) {
            await resourcesStore.updateResource(resource.id, resourceData);
        } else {
            await resourcesStore.addResource(resourceData);
        }

        dispatch('submit');
    } catch (e) {
        error = e.message;
    } finally {
        submitting = false;
    }
}

function handleCancel() {
    dispatch('close');
}
</script>

<div class="resource-form-overlay">
    <div class="resource-form">
        <h2>{resource ? 'Redigera resurs' : 'Lägg till ny resurs'}</h2>

        {#if error}
            <div class="error-message">{error}</div>
        {/if}

        <form on:submit|preventDefault={handleSubmit}>
            <div class="form-field">
                <label for="title">Titel *</label>
                <input
                    type="text"
                    id="title"
                    bind:value={title}
                    placeholder="Ange titel"
                    required
                />
            </div>

            <div class="form-field">
                <label for="description">Beskrivning</label>
                <textarea
                    id="description"
                    bind:value={description}
                    placeholder="Ange beskrivning"
                    rows="4"
                ></textarea>
            </div>

            <div class="form-field">
                <label for="type">Typ</label>
                <select id="type" bind:value={type}>
                    {#each resourceTypes as resourceType}
                        <option value={resourceType.value}>
                            {resourceType.label}
                        </option>
                    {/each}
                </select>
            </div>

            <div class="form-field">
                <label for="status">Status</label>
                <select id="status" bind:value={status}>
                    {#each statusTypes as statusType}
                        <option value={statusType.value}>
                            {statusType.label}
                        </option>
                    {/each}
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="button" on:click={handleCancel} disabled={submitting}>
                    Avbryt
                </button>
                <button type="submit" class="button button-primary" disabled={submitting}>
                    {submitting ? 'Sparar...' : (resource ? 'Uppdatera' : 'Skapa')}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.resource-form-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 160000;
}

.resource-form {
    background: white;
    padding: 30px;
    border-radius: 4px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-field {
    margin-bottom: 20px;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-field input,
.form-field textarea,
.form-field select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.error-message {
    color: #d63638;
    margin-bottom: 20px;
    padding: 10px;
    background: #ffebe8;
    border: 1px solid #d63638;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
</style>
