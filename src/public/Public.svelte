<script>
export let wpData;

let resources = [];
let bookings = [];

async function fetchResources() {
  try {
    const response = await fetch(`${wpData.apiUrl}/resources`, {
      headers: {
        'X-WP-Nonce': wpData.nonce
      }
    });
    if (!response.ok) throw new Error('Failed to fetch resources');
    resources = await response.json();
  } catch (error) {
    console.error('Error fetching resources:', error);
    resources = [];
  }
}

async function fetchBookings() {
  try {
    const response = await fetch(`${wpData.apiUrl}/bookings`, {
      headers: {
        'X-WP-Nonce': wpData.nonce
      }
    });
    if (!response.ok) throw new Error('Failed to fetch bookings');
    bookings = await response.json();
  } catch (error) {
    console.error('Error fetching bookings:', error);
    bookings = [];
  }
}

fetchResources();
fetchBookings();
</script>

<main>
  <h1>Resource Booking</h1>
  
  <section>
    <h2>Available Resources</h2>
    {#if resources.length > 0}
      <ul>
        {#each resources as resource}
          <li>{resource.name} ({resource.type})</li>
        {/each}
      </ul>
    {:else}
      <p>No resources available</p>
    {/if}
  </section>
  
  <section>
    <h2>Current Bookings</h2>
    {#if bookings.length > 0}
      <ul>
        {#each bookings as booking}
          <li>{booking.resource_name} - {booking.start_time} to {booking.end_time}</li>
        {/each}
      </ul>
    {:else}
      <p>No current bookings</p>
    {/if}
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