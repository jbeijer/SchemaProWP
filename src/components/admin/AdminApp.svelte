<script>
  import Organizations from './Organizations.svelte';
  import Resources from './Resources.svelte';

  let activeTab = 'organizations';
  
  console.log('AdminApp: Mounted');
</script>

<div class="admin-app">
  <h1>SchemaProWP Admin</h1>
  
  <nav>
    <button class:active={activeTab === 'organizations'} on:click={() => activeTab = 'organizations'}>
      Organisationer
    </button>
    <button class:active={activeTab === 'resources'} on:click={() => activeTab = 'resources'}>
      Resurser
    </button>
  </nav>

  {#if activeTab === 'organizations'}
    <Organizations />
  {:else if activeTab === 'resources'}
    <Resources />
  {/if}
</div>

<style>
  .admin-app {
    background-color: #f0f0f0;
    padding: 20px;
    border-radius: 5px;
  }

  h1 {
    color: #333;
    margin-top: 0;
  }

  nav {
    margin-bottom: 20px;
  }

  button {
    padding: 10px 15px;
    margin-right: 10px;
    background-color: #ddd;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
  }

  button:hover {
    background-color: #ccc;
  }

  button.active {
    background-color: #007bff;
    color: white;
  }
</style>