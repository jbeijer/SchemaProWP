import './app.css';
import App from './App.svelte';

const app = new App({
  target: document.getElementById('schemaprowp-admin'),
  props: {
    data: window.schemaProWPData || {}
  }
});

export default app;
