<script>
import { onMount } from 'svelte';
import { resourcesStore } from '../stores/resources.js';

let stats = {
    totalResources: 0,
    activeResources: 0,
    totalBookings: 0,
    upcomingBookings: 0
};

onMount(async () => {
    try {
        await resourcesStore.fetchResources();
        updateStats();
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
    }
});

function updateStats() {
    const resources = $resourcesStore.items;
    stats = {
        totalResources: resources.length,
        activeResources: resources.filter(r => r.status === 'active').length,
        totalBookings: 0, // Will be updated when we add bookings store
        upcomingBookings: 0 // Will be updated when we add bookings store
    };
}
</script>

<div class="dashboard">
    <div class="dashboard-header">
        <h2>Översikt</h2>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Totalt antal resurser</h3>
            <div class="stat-number">{stats.totalResources}</div>
        </div>
        <div class="stat-card">
            <h3>Aktiva resurser</h3>
            <div class="stat-number">{stats.activeResources}</div>
        </div>
        <div class="stat-card">
            <h3>Totalt antal bokningar</h3>
            <div class="stat-number">{stats.totalBookings}</div>
        </div>
        <div class="stat-card">
            <h3>Kommande bokningar</h3>
            <div class="stat-number">{stats.upcomingBookings}</div>
        </div>
    </div>

    <div class="dashboard-sections">
        <section class="quick-actions">
            <h3>Snabbåtgärder</h3>
            <div class="action-buttons">
                <a href="#/resources/new" class="button button-primary">
                    Lägg till ny resurs
                </a>
                <a href="#/bookings/new" class="button">
                    Skapa ny bokning
                </a>
            </div>
        </section>

        {#if $resourcesStore.items.length > 0}
            <section class="recent-resources">
                <h3>Senaste resurser</h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Titel</th>
                            <th>Typ</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each $resourcesStore.items.slice(0, 5) as resource}
                            <tr>
                                <td>{resource.title}</td>
                                <td>{resource.type}</td>
                                <td>{resource.status}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </section>
        {/if}
    </div>
</div>

<style>
.dashboard {
    padding: 20px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #23282d;
}

.stat-number {
    font-size: 24px;
    font-weight: 600;
    color: #1e1e1e;
}

.dashboard-sections {
    display: grid;
    gap: 30px;
}

.quick-actions {
    margin-bottom: 30px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.recent-resources {
    margin-top: 20px;
}

h3 {
    margin-bottom: 15px;
}
</style>
