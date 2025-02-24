<script>
  import { onMount } from 'svelte';
  import { Button, Card } from 'flowbite-svelte';
  import { format } from 'date-fns';
  import { resources, selectedResource, fetchResources } from '../stores/resources.store';
  import { bookings, fetchBookings } from '../stores/bookings.store';
  import Calendar from '@event-calendar/core';
  import TimeGrid from '@event-calendar/time-grid';
  
  let calendar;
  let calendarEl;
  let currentDate = new Date();
  let viewType = 'timeGridWeek'; // 'timeGridDay', 'timeGridWeek'
  
  $: calendarOptions = {
    plugins: [TimeGrid],
    view: viewType,
    date: currentDate,
    events: $bookings ? $bookings.map(booking => ({
      id: booking.id,
      start: booking.start_time,
      end: booking.end_time,
      title: `Bokning: ${$selectedResource?.name || ''}`,
      backgroundColor: getStatusColor(booking.status)
    })) : [],
    headerToolbar: false,
    slotDuration: '00:15:00',
    slotMinTime: '08:00:00',
    slotMaxTime: '20:00:00',
    allDaySlot: false,
    locale: 'sv',
    height: 'auto',
    eventClick: info => handleEventClick(info.event)
  };
  
  function getStatusColor(status) {
    switch (status) {
      case 'confirmed':
        return '#22c55e'; // green-500
      case 'pending':
        return '#eab308'; // yellow-500
      case 'cancelled':
        return '#ef4444'; // red-500
      default:
        return '#3b82f6'; // blue-500
    }
  }
  
  function handleEventClick(event) {
    // Implementera hantering av klick på bokning
    console.log('Clicked booking:', event);
  }
  
  $: {
    if ($selectedResource && calendar) {
      const start = calendar.view.activeStart;
      const end = calendar.view.activeEnd;
      fetchBookings($selectedResource.id, start.toISOString(), end.toISOString());
    }
  }
  
  onMount(() => {
    fetchResources();
    calendar = new Calendar(calendarEl, calendarOptions);
  });
</script>

<div class="container mx-auto p-4">
  <Card class="mb-4">
    <div class="flex justify-between items-center mb-4">
      <div class="flex space-x-2">
        <Button size="sm" color="light" on:click={() => viewType = 'timeGridDay'}>Dag</Button>
        <Button size="sm" color="light" on:click={() => viewType = 'timeGridWeek'}>Vecka</Button>
      </div>
      <div class="text-xl font-semibold">
        {format(currentDate, 'MMMM yyyy')}
      </div>
      <div class="flex space-x-2">
        <Button size="sm" on:click={() => {
          currentDate = new Date();
          calendar.setOption('date', currentDate);
        }}>Idag</Button>
        <Button size="sm" color="light" on:click={() => {
          currentDate.setDate(currentDate.getDate() - 7);
          currentDate = new Date(currentDate);
          calendar.setOption('date', currentDate);
        }}>←</Button>
        <Button size="sm" color="light" on:click={() => {
          currentDate.setDate(currentDate.getDate() + 7);
          currentDate = new Date(currentDate);
          calendar.setOption('date', currentDate);
        }}>→</Button>
      </div>
    </div>
    
    <div bind:this={calendarEl} class="min-h-[600px]"></div>
  </Card>
</div>

<style lang="postcss">
  :global(.ec-header) {
    @apply bg-gray-50 border-b border-gray-200;
  }
  
  :global(.ec-day-grid) {
    @apply border-gray-200;
  }
  
  :global(.ec-event) {
    @apply rounded-md shadow-sm;
  }
  
  :global(.ec-toolbar button) {
    @apply bg-white border border-gray-300 rounded-md px-3 py-1 text-sm 
           hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500;
  }
  
  :global(.ec-toolbar button.ec-active) {
    @apply bg-blue-500 text-white border-blue-500;
  }
</style>
