import Admin from './Admin.svelte';

const target = document.getElementById('schemaprowp-app');
const wpData = JSON.parse(target.dataset.wpData || '{}');

const app = new Admin({
  target,
  props: {
    wpData
  }
});

export default app;