import Public from './Public.svelte';

function initApp() {
    console.log('SchemaProWP: Initierar och söker efter kalenderbehållare');
    
    // Look for all calendar containers
    const containers = document.querySelectorAll('.schemaprowp-calendar');
    
    console.log(`SchemaProWP: Hittade ${containers.length} behållare`);
    
    if (containers.length === 0) {
        console.error('SchemaProWP: Ingen kalenderbehållare hittades');
        return null;
    }
    
    // Use global data if available
    const wpData = window.schemaProWPData || {};
    
    // Create an app instance for each container
    return Array.from(containers).map(container => {
        try {
            // Try to read data from container attributes
            let containerData = {};
            if (container.dataset.wpData) {
                try {
                    containerData = JSON.parse(container.dataset.wpData);
                } catch (error) {
                    console.warn('SchemaProWP: Kunde inte tolka data-wp-data attribut:', error);
                }
            }
            
            // Combine data
            const finalData = {...wpData, ...containerData};
            
            console.log('SchemaProWP: Initierar app med data:', finalData);
            
            const app = new Public({
                target: container,
                props: {
                    wpData: finalData
                }
            });
            
            console.log('SchemaProWP: App initierad framgångsrikt');
            return app;
            
        } catch (error) {
            console.error('SchemaProWP: Fel vid initiering av app:', error);
            
            // Show error message in development mode
            if (process.env.NODE_ENV === 'development') {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'schemaprowp-error';
                errorDiv.style.cssText = 'color: red; padding: 1em; border: 1px solid red; margin: 1em 0;';
                errorDiv.textContent = `Fel vid initiering av kalender: ${error.message}`;
                container.parentNode.insertBefore(errorDiv, container);
            }
            return null;
        }
    }).filter(Boolean); // Filter out any null instances from errors
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

export default {};