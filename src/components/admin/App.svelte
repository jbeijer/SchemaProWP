<script>
  import Organizations from './Organizations.svelte';
  import Resources from './Resources.svelte';

  let activeTab = 'organizations';
</script>

<main>
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
</main>

<style>
  main {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }

  h1 {
    color: #333;
  }

  nav {
    margin-bottom: 20px;
  }

  button {
    padding: 10px 15px;
    margin-right: 10px;
    background-color: #f0f0f0;
    border: none;
    cursor: pointer;
  }

  button.active {
    background-color: #007bff;
    color: white;
  }
</style>