import './app.css';
import App from './App.svelte';

function initAdmin() {
  const target = document.getElementById('schemaprowp-admin');
  
  if (!target) {
    console.error('SchemaProWP: Admin container not found');
    return;
  }
  
  try {
    const app = new App({
      target,
      props: {
        data: window.schemaProWPData || {}
      }
    });
    
    return app;
  } catch (error) {
    console.error('SchemaProWP: Failed to initialize admin app:', error);
  }
}

// Vänta tills DOM är redo
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAdmin);
} else {
  initAdmin();
}

export default {};
