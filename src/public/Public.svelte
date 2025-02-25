<script>
import { onMount } from 'svelte';
import { resources, bookings, loading, errors, fetchResources, fetchBookings } from '../stores/resources.store.js';

export let wpData;

onMount(() => {
    fetchResources(wpData.apiUrl, wpData.nonce);
    fetchBookings(wpData.apiUrl, wpData.nonce);
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
                    </div>
                {/each}
            </div>
        {/if}
    </section>

    <!-- Bookings Section -->
    <section class="bookings-section">
        <h2>Bookings</h2>
        
        {#if $loading.bookings}
            <div class="loading">Loading bookings...</div>
        {:else if $errors.bookings}
            <div class="error">
                <p>Error loading bookings: {$errors.bookings}</p>
                <button on:click={() => fetchBookings(wpData.apiUrl, wpData.nonce)}>
                    Retry
                </button>
            </div>
        {:else}
            <div class="bookings-list">
                {#each $bookings as booking}
                    <div class="booking-item">
                        <h3>{booking.title}</h3>
                        <p>Date: {booking.date}</p>
                        <p>Status: {booking.status}</p>
                    </div>
                {/each}
            </div>
        {/if}
    </section>
</div>

<style>
    .schema-pro-wp-public {
        padding: 1rem;
    }

    .loading {
        text-align: center;
        padding: 2rem;
        color: #666;
    }

    .error {
        background: #fff5f5;
        border: 1px solid #feb2b2;
        padding: 1rem;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }

    .error button {
        background: #e53e3e;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        border: none;
        margin-top: 0.5rem;
        cursor: pointer;
    }

    .error button:hover {
        background: #c53030;
    }

    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .resource-card {
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .bookings-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1rem;
    }

    .booking-item {
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>