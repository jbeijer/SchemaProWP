<script>
  import { onMount } from 'svelte';
  import Calendar from './components/Calendar.svelte';
  import ResourceList from './components/ResourceList.svelte';
  import BookingForm from './components/BookingForm.svelte';
  
  export let wpData = {};
  let selectedDate = new Date();
  let view = wpData.view || 'month';
  let organization = wpData.organization || '';
  
  onMount(() => {
    console.log('SchemaProWP: App monterad med data:', wpData);
  });
</script>

<div class="min-h-screen bg-gray-50 py-8">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
      <!-- Sidopanel med resurser -->
      <div class="md:col-span-3">
        <ResourceList {organization} />
      </div>
      
      <!-- Huvudinnehåll -->
      <div class="md:col-span-9">
        <div class="space-y-6">
          <Calendar bind:selectedDate {view} />
          <BookingForm {selectedDate} {organization} />
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  :global(body) {
    @apply antialiased text-gray-900;
  }
  
  :global(.container) {
    @apply max-w-7xl;
  }
</style>
