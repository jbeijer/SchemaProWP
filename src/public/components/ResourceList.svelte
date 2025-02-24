<script>
  import { Card, Button, Badge } from 'flowbite-svelte';
  import { resources, selectedResource } from '../stores/resources.store';
  
  function selectResource(resource) {
    selectedResource.set(resource);
  }
  
  function handleKeyDown(event, resource) {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      selectResource(resource);
    }
  }
  
  function getResourceTypeIcon(type) {
    switch (type) {
      case 'room':
        return '🏠';
      case 'equipment':
        return '🔧';
      case 'vehicle':
        return '🚗';
      default:
        return '📦';
    }
  }
</script>

<Card class="mb-4">
  <h5 class="mb-4 text-xl font-bold text-gray-900">Resurser</h5>
  
  <div class="divide-y divide-gray-200">
    {#if $resources && $resources.length > 0}
      {#each $resources as resource}
        <div
          role="button"
          tabindex="0"
          class="flex items-center justify-between p-3 hover:bg-gray-50 cursor-pointer transition-colors
                 {$selectedResource?.id === resource.id ? 'bg-blue-50' : ''}"
          on:click={() => selectResource(resource)}
          on:keydown={(e) => handleKeyDown(e, resource)}
        >
          <div class="flex items-center space-x-3">
            <span class="text-xl" aria-hidden="true">{getResourceTypeIcon(resource.type)}</span>
            <div>
              <p class="font-medium text-gray-900">{resource.name}</p>
              <p class="text-sm text-gray-500">{resource.type}</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-2">
            <Badge
              color={resource.status === 'active' ? 'green' : 'gray'}
              class="px-2.5 py-0.5 text-xs"
            >
              {resource.status}
            </Badge>
          </div>
        </div>
      {/each}
    {:else}
      <div class="p-4">
        <p class="text-gray-500 text-center">Inga resurser tillgängliga</p>
      </div>
    {/if}
  </div>
</Card>

<style lang="postcss">
  :global(.resource-list-item) {
    @apply transition-all duration-200;
  }
  
  :global(.resource-list-item:hover) {
    @apply transform scale-[1.01];
  }
</style>
