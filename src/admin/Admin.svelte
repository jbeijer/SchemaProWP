<script>
import { Router, Link, Route } from 'svelte-spa-router';
import ResourceList from '../components/ResourceList.svelte';
import Dashboard from '../components/Dashboard.svelte';

export let wpData;

const routes = {
    '/': Dashboard,
    '/resources': ResourceList,
    // Add more routes as needed
};
</script>

<main class="schemaprowp-admin-app">
    <nav class="nav-tab-wrapper">
        <a href="#/" class="nav-tab" class:nav-tab-active={location.hash === '#/' || location.hash === ''}>
            Översikt
        </a>
        <a href="#/resources" class="nav-tab" class:nav-tab-active={location.hash === '#/resources'}>
            Resurser
        </a>
    </nav>

    <div class="schemaprowp-admin-content">
        <Router {routes} />
    </div>
</main>

<style>
.schemaprowp-admin-app {
    margin: 20px 0;
}

.schemaprowp-admin-content {
    margin-top: 20px;
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

:global(.nav-tab) {
    margin-left: 0.5em;
    margin-right: 0.5em;
}

:global(.nav-tab:first-child) {
    margin-left: 0;
}
</style>