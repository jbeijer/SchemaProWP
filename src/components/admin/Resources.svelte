<script>
  let resources = [];
  let newResourceName = '';
  let newResourceType = 'car'; // Default type

  // Simulerad funktion för att hämta resurser
  async function fetchResources() {
    // Här skulle vi normalt göra en API-anrop
    resources = [
      { id: 1, name: 'Volvo XC60', type: 'car', status: 'available' },
      { id: 2, name: 'Konferensrum A', type: 'room', status: 'booked' },
      { id: 3, name: 'Projektor 1', type: 'equipment', status: 'maintenance' },
    ];
  }

  // Simulerad funktion för att lägga till en resurs
  async function addResource() {
    if (newResourceName.trim()) {
      // Här skulle vi normalt göra ett API-anrop för att skapa resursen
      resources = [...resources, { id: resources.length + 1, name: newResourceName, type: newResourceType, status: 'available' }];
      newResourceName = '';
    }
  }

  // Hämta resurser när komponenten laddas
  fetchResources();
</script>

<div>
  <h2>Resurser</h2>
  
  <form on:submit|preventDefault={addResource}>
    <input bind:value={newResourceName} placeholder="Ny resurs" />
    <select bind:value={newResourceType}>
      <option value="car">Bil</option>
      <option value="room">Rum</option>
      <option value="equipment">Utrustning</option>
    </select>
    <button type="submit">Lägg till</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Namn</th>
        <th>Typ</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      {#each resources as resource}
        <tr>
          <td>{resource.name}</td>
          <td>{resource.type}</td>
          <td>{resource.status}</td>
        </tr>
      {/each}
    </tbody>
  </table>
</div>

<style>
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    text-align: left;
    padding: 8px;
    border-bottom: 1px solid #ddd;
  }
  th {
    background-color: #f2f2f2;
  }
  form {
    margin-bottom: 20px;
  }
  input, select {
    padding: 5px;
    margin-right: 10px;
  }
  button {
    padding: 5px 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
  }
</style>