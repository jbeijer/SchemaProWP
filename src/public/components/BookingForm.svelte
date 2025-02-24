<script>
  import { Card, Button, Label, Input, Textarea, Alert } from 'flowbite-svelte';
  import { selectedResource } from '../stores/resources.store';
  import { bookings, fetchBookings } from '../stores/bookings.store';
  import { format } from 'date-fns';
  
  export let selectedDate;
  
  let startTime = '';
  let endTime = '';
  let comments = '';
  let error = '';
  let success = '';
  let loading = false;
  
  $: if (selectedDate) {
    const date = format(selectedDate, 'yyyy-MM-dd');
    startTime = `${date}T09:00`;
    endTime = `${date}T10:00`;
  }
  
  async function handleSubmit() {
    if (!$selectedResource) {
      error = 'Välj en resurs först';
      return;
    }
    
    if (!startTime || !endTime) {
      error = 'Välj start- och sluttid';
      return;
    }
    
    const booking = {
      resource_id: $selectedResource.id,
      start_time: new Date(startTime).toISOString(),
      end_time: new Date(endTime).toISOString(),
      comments: comments,
      status: 'pending'
    };
    
    try {
      loading = true;
      error = '';
      success = '';
      
      const response = await fetch(`${window.schemaProWPData.restUrl}/bookings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.schemaProWPData.nonce
        },
        body: JSON.stringify(booking)
      });
      
      if (!response.ok) {
        const data = await response.json();
        throw new Error(data.message || 'Ett fel uppstod');
      }
      
      success = 'Bokning skapad!';
      comments = '';
      
      // Uppdatera bokningslistan
      if ($selectedResource) {
        fetchBookings(
          $selectedResource.id,
          new Date(startTime).toISOString(),
          new Date(endTime).toISOString()
        );
      }
      
    } catch (e) {
      error = e.message;
    } finally {
      loading = false;
    }
  }
</script>

<Card class="mb-4">
  <form on:submit|preventDefault={handleSubmit} class="space-y-4">
    <h5 class="text-xl font-bold text-gray-900 mb-4">Boka {$selectedResource ? $selectedResource.name : 'resurs'}</h5>
    
    {#if error}
      <Alert color="red" class="mb-4">
        {error}
      </Alert>
    {/if}
    
    {#if success}
      <Alert color="green" class="mb-4">
        {success}
      </Alert>
    {/if}
    
    <div class="grid grid-cols-2 gap-4">
      <div>
        <Label for="start-time" class="mb-2">Starttid</Label>
        <Input
          type="datetime-local"
          id="start-time"
          bind:value={startTime}
          required
        />
      </div>
      
      <div>
        <Label for="end-time" class="mb-2">Sluttid</Label>
        <Input
          type="datetime-local"
          id="end-time"
          bind:value={endTime}
          required
        />
      </div>
    </div>
    
    <div>
      <Label for="comments" class="mb-2">Kommentarer</Label>
      <Textarea
        id="comments"
        bind:value={comments}
        rows="3"
        placeholder="Skriv eventuella kommentarer här..."
      />
    </div>
    
    <div class="flex justify-end">
      <Button type="submit" disabled={loading}>
        {#if loading}
          <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
        {/if}
        Boka nu
      </Button>
    </div>
  </form>
</Card>

<style>
  :global(.booking-form-input) {
    @apply transition-all duration-200;
  }
  
  :global(.booking-form-input:focus) {
    @apply ring-2 ring-blue-500;
  }
</style>
