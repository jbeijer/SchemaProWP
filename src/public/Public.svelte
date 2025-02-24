<script>
export let wpData;

let resources = [];
let bookings = [];

async function fetchResources() {
  const response = await fetch(`${wpData.ajaxUrl}?action=schemaprowp_get_public_resources&_wpnonce=${wpData.nonce}`);
  resources = await response.json();
}

async function fetchBookings() {
  const response = await fetch(`${wpData.ajaxUrl}?action=schemaprowp_get_public_bookings&_wpnonce=${wpData.nonce}`);
  bookings = await response.json();
}

fetchResources();
fetchBookings();
</script>

<main>
  <h1>Resource Booking</h1>
  
  <section>
    <h2>Available Resources</h2>
    <ul>
      {#each resources as resource}
        <li>{resource.name} ({resource.type})</li>
      {/each}
    </ul>
  </section>
  
  <section>
    <h2>Current Bookings</h2>
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