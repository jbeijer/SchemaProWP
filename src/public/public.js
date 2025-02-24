import Public from './Public.svelte';

function initApp() {
    // Look for all calendar containers
    const containers = document.querySelectorAll('.schemaprowp-calendar');
    
    if (containers.length === 0) {
        console.error('SchemaProWP: No calendar containers found');
        return null;
    }
    
    // Use global data if available
    const wpData = window.schemaProWPData || {};
    
    // Create an app instance for each container
    return Array.from(containers).map(container => {
        // Try to read data from container attributes
        let containerData = {};
        if (container.dataset.wpData) {
            try {
                containerData = JSON.parse(container.dataset.wpData);
            } catch (error) {
                console.warn('SchemaProWP: Could not parse data-wp-data attribute:', error);
            }
        }
        
        // Combine data
        const finalData = {...wpData, ...containerData};
        
        return new Public({
            target: container,
            props: {
                wpData: finalData
            }
        });
    });
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

export default {};