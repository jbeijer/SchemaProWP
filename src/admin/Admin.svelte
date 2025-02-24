<script>
export let wpData;

let organizations = [];
let resources = [];
let bookings = [];

async function fetchOrganizations() {
  const response = await fetch(`${wpData.ajaxUrl}?action=schemaprowp_get_organizations&_wpnonce=${wpData.nonce}`);
  organizations = await response.json();
}

async function fetchResources() {
  const response = await fetch(`${wpData.ajaxUrl}?action=schemaprowp_get_resources&_wpnonce=${wpData.nonce}`);
  resources = await response.json();
}

async function fetchBookings() {
  const response = await fetch(`${wpData.ajaxUrl}?action=schemaprowp_get_bookings&_wpnonce=${wpData.nonce}`);
  bookings = await response.json();
}

fetchOrganizations();
fetchResources();
fetchBookings();
</script>

<main>
  <h1>SchemaProWP Admin</h1>
  
  <section>
    <h2>Organizations</h2>
    <ul>
      {#each organizations as org}
        <li>{org.name}</li>
      {/each}
    </ul>
  </section>
  
  <section>
    <h2>Resources</h2>
    <ul>
      {#each resources as resource}
        <li>{resource.name} ({resource.type})</li>
      {/each}
    </ul>
  </section>
  
  <section>
    <h2>Bookings</h2>
    <ul>
      {#each bookings as booking}
        <li>{booking.resource_name} - {booking.start_time} to {booking.end_time}</li>
      {/each}
    </ul>
  </section>
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
  
  section {
    margin-bottom: 20px;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  li {
    margin-bottom: 5px;
  }
</style>