<script>
    import { Router, Route } from 'svelte-routing';
    import routes from './routes/+layout.svelte';
    import { onMount } from 'svelte';
    import { resources, loading, errors, fetchResources } from '../stores/resources.store.js';
    import CreateResource from './components/CreateResource.svelte';
    import CreateOrganization from './components/CreateOrganization.svelte';

    export let wpData;

    onMount(() => {
        fetchResources(wpData.apiUrl, wpData.nonce, wpData.post_id);
    });
</script>

<Router>
    <Route path="/" component={routes} wpData={wpData} />
</Router>

<style>
/* All styles removed since they're unused */
</style>