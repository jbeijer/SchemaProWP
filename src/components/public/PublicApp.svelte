<script>
  let resources = [];
  
  // Simulerad funktion för att hämta tillgängliga resurser
  async function fetchAvailableResources() {
    // Här skulle vi normalt göra ett API-anrop
    resources = [
      { id: 1, name: 'Volvo XC60', type: 'car', status: 'available' },
      { id: 2, name: 'Konferensrum A', type: 'room', status: 'available' },
      { id: 3, name: 'Projektor 1', type: 'equipment', status: 'available' },
    ];
  }

  // Hämta resurser när komponenten laddas
  fetchAvailableResources();
</script>

<div class="public-app">
  <h1>SchemaProWP Bokningssystem</h1>
  
  <h2>Tillgängliga Resurser</h2>
  <ul>
    {#each resources as resource}
      <li>
        {resource.name} ({resource.type})
        <button>Boka</button>
      </li>
    {/each}
  </ul>
</div>

<style>
  .public-app {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }

  h1, h2 {
    color: #333;
  }

  ul {
    list-style-type: none;
    padding: 0;
  }

  li {
    margin-bottom: 10px;
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 3px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  button {
    padding: 5px 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
  }
</style>