import Admin from './admin/Admin.svelte';
import Public from './public/Public.svelte';

const target = document.getElementById('schemaprowp-app');
const isAdmin = target.dataset.isAdmin === '1';

const app = new (isAdmin ? Admin : Public)({
  target,
  props: {
    wpData: JSON.parse(target.dataset.wpData || '{}')
  }
});

export default app;