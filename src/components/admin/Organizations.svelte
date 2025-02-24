<script>
  let organizations = [];
  let newOrganizationName = '';

  // Simulerad funktion för att hämta organisationer
  async function fetchOrganizations() {
    // Här skulle vi normalt göra en API-anrop
    organizations = [
      { id: 1, name: 'Huvudkontor', parent_id: null },
      { id: 2, name: 'Regionkontor Nord', parent_id: 1 },
      { id: 3, name: 'Regionkontor Syd', parent_id: 1 },
    ];
  }

  // Simulerad funktion för att lägga till en organisation
  async function addOrganization() {
    if (newOrganizationName.trim()) {
      // Här skulle vi normalt göra ett API-anrop för att skapa organisationen
      organizations = [...organizations, { id: organizations.length + 1, name: newOrganizationName, parent_id: null }];
      newOrganizationName = '';
    }
  }

  // Hämta organisationer när komponenten laddas
  fetchOrganizations();
</script>

<div>
  <h2>Organisationer</h2>
  
  <form on:submit|preventDefault={addOrganization}>
    <input bind:value={newOrganizationName} placeholder="Ny organisation" />
    <button type="submit">Lägg till</button>
  </form>

  <ul>
    {#each organizations as org}
      <li>{org.name}</li>
    {/each}
  </ul>
</div>

<style>
  ul {
    list-style-type: none;
    padding: 0;
  }
  li {
    margin-bottom: 10px;
    padding: 5px;
    background-color: #f0f0f0;
    border-radius: 3px;
  }
  form {
    margin-bottom: 20px;
  }
  input {
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