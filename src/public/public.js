import Public from './Public.svelte';

function initApp() {
    const target = document.getElementById('schemaprowp-app');
    if (!target) {
        console.error('SchemaProWP: Could not find target element #schemaprowp-app');
        return null;
    }

    // Use the global schemaProWPData object that was localized by WordPress
    if (!window.schemaProWPData) {
        console.error('SchemaProWP: Required WordPress data not found');
        return null;
    }

    return new Public({
        target,
        props: {
            wpData: window.schemaProWPData
        }
    });
}

export default initApp();