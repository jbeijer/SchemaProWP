import './app.css';
import App from './App.svelte';

function initPublic() {
  const containers = document.querySelectorAll('.schemaprowp-calendar');
  
  if (!containers.length) {
    console.error('SchemaProWP: No calendar containers found');
    return;
  }
  
  try {
    const apps = Array.from(containers).map(container => {
      const data = {
        ...window.schemaProWPData,
        containerId: container.id,
        settings: JSON.parse(container.dataset.settings || '{}')
      };
      
      return new App({
        target: container,
        props: { data }
      });
    });
    
    return apps;
  } catch (error) {
    console.error('SchemaProWP: Failed to initialize calendar:', error);
  }
}

// Vänta tills DOM är redo
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPublic);
} else {
  initPublic();
}

export default {};
