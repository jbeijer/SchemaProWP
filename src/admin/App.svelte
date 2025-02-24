<script>
  import { onMount } from 'svelte';
  
  export let data = {};

  onMount(() => {
    console.log('SchemaProWP Admin Data:', data);
  });
</script>

<div class="wrap">
  <h1>SchemaProWP Admin</h1>
  <div class="admin-content">
    <!-- Admin-innehåll kommer här -->
  </div>
</div>

<style lang="postcss">
  .wrap {
    @apply p-4;
  }
  
  .admin-content {
    @apply mt-4 bg-white p-4 rounded-lg shadow;
  }
</style>
