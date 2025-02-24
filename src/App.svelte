<script>
  import AdminApp from './components/admin/AdminApp.svelte';
  import PublicApp from './components/public/PublicApp.svelte';
  import { setContext } from 'svelte';
  import { appState } from './stores';

  // Subscribe to the store
  let isAdmin = false;
  let wpData = {};
  
  appState.subscribe(state => {
    isAdmin = state.isAdmin;
    wpData = state.wpData;
    // Set up context for child components whenever state changes
    setContext('wpData', wpData);
  });

  console.log('App.svelte: Mounted with state:', { isAdmin, wpData });
</script>

<main>
  {#if isAdmin}
    <AdminApp />
  {:else}
    <PublicApp />
  {/if}
</main>

<style>
  main {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
</style>