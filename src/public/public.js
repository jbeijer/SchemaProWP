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
    
    // Validate and normalize API URL
    if (!wpData.restUrl && !wpData.apiUrl) {
        console.error('SchemaProWP: Ingen API URL tillgänglig');
        return null;
    }
    
    // Normalize API URL
    wpData.apiUrl = wpData.restUrl || wpData.apiUrl;
    if (!wpData.apiUrl.endsWith('/')) {
        wpData.apiUrl += '/';
    }
    wpData.apiUrl += 'schemaprowp/v1';
    
    console.log('SchemaProWP: Använder API URL:', wpData.apiUrl);
    
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
            const finalData = {
                ...wpData,
                ...containerData,
                debug: window.__DEV__ || false
            };
            
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
            container.innerHTML = `
                <div class="schemaprowp-error">
                    <p>Ett fel uppstod vid initiering av bokningskalendern.</p>
                    ${window.__DEV__ ? `<pre>${error.message}</pre>` : ''}
                </div>
            `;
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