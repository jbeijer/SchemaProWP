import Public from './Public.svelte';

function initApp() {
    const target = document.getElementById('schemaprowp-app');
    let app = null;

    if (!target) {
        console.error('SchemaProWP: Could not find target element #schemaprowp-app');
        return null;
    }

    // Försök hämta data från både dataset och global variabel
    let wpData = {};
    try {
        wpData = target.dataset.wpData ? JSON.parse(target.dataset.wpData) : {};
    } catch (e) {
        console.warn('SchemaProWP: Failed to parse data from dataset');
    }

    // Använd global variabel som fallback
    if (Object.keys(wpData).length === 0 && window.schemaProWPData) {
        wpData = window.schemaProWPData;
    }

    return new Public({
        target,
        props: {
            wpData
        }
    });
}

export default initApp();