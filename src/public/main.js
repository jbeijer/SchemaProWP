import './app.css';
import App from './App.svelte';

function initPublic() {
  const debug = window.schemaProWPData?.debug || false;
  
  if (debug) {
    console.log('Initializing SchemaProWP public app...');
    console.log('Global data:', window.schemaProWPData);
  }
  
  const containers = document.querySelectorAll('.schemaprowp-calendar');
  
  if (debug) {
    console.log('Found containers:', containers.length);
  }
  
  if (!containers.length) {
    console.error('SchemaProWP: No calendar containers found. Make sure you have added the [schemaprowp] shortcode to your page.');
    return;
  }
  
  try {
    const apps = Array.from(containers).map(container => {
      if (debug) {
        console.log('Container:', container);
        console.log('Container dataset:', container.dataset);
      }
      
      // Merge global data with container-specific settings
      const settings = container.dataset.settings ? JSON.parse(container.dataset.settings) : {};
      const data = {
        ...window.schemaProWPData,
        containerId: container.id,
        settings
      };
      
      if (debug) {
        console.log('Initializing app with data:', data);
      }
      
      return new App({
        target: container,
        props: { data }
      });
    });
    
    if (debug) {
      console.log('Successfully initialized', apps.length, 'calendar instances');
    }
    
    return apps;
  } catch (error) {
    console.error('SchemaProWP: Failed to initialize calendar:', error);
    // Add visible error message in development
    if (debug) {
      const errorDiv = document.createElement('div');
      errorDiv.className = 'schemaprowp-error';
      errorDiv.style.cssText = 'color: red; padding: 1em; border: 1px solid red; margin: 1em 0;';
      errorDiv.textContent = `Failed to initialize calendar: ${error.message}`;
      containers[0].parentNode.insertBefore(errorDiv, containers[0]);
    }
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPublic);
} else {
  initPublic();
}

export default {};
