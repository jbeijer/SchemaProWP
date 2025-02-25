<script>
    import { Router, Route, Link, useLocation } from 'svelte-routing';
    const location = useLocation();
</script>

<nav class="schema-pro-nav">
    <a href="/" class:active={$location.pathname === '/'}><Link href="/">Resources</Link></a>
    <a href="/create-resource" class:active={$location.pathname === '/create-resource'}><Link href="/create-resource">Create Resource</Link></a>
    <a href="/create-organization" class:active={$location.pathname === '/create-organization'}><Link href="/create-organization">Create Organization</Link></a>
</nav>

<style>
.schema-pro-nav {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.schema-pro-nav a {
    text-decoration: none;
    color: #333;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.schema-pro-nav a:hover {
    background-color: #f5f5f5;
}

.schema-pro-nav a.active {
    background-color: #1976d2;
    color: white;
}
</style>
