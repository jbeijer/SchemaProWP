import './app.css';
import App from './App.svelte';

function initPublic() {
    console.log('SchemaProWP: DOM laddad, söker efter kalenderbehållare');
    
    const containers = document.querySelectorAll('.schemaprowp-calendar');
    
    console.log(`SchemaProWP: Hittade ${containers.length} behållare`);
    
    if (!containers.length) {
        console.error('SchemaProWP: Ingen kalenderbehållare hittades');
        return;
    }
    
    containers.forEach(container => {
        try {
            let containerData = {};
            if (container.dataset.wpData) {
                try {
                    containerData = JSON.parse(container.dataset.wpData);
                } catch (e) {
                    console.warn('SchemaProWP: Kunde inte tolka data-wp-data attribut', e);
                }
            }
            
            const appData = {
                ...window.schemaProWPData || {},
                ...containerData
            };
            
            console.log('SchemaProWP: Initierar app med data:', appData);
            
            const app = new App({
                target: container,
                props: { wpData: appData }
            });
            
            console.log('SchemaProWP: App initierad framgångsrikt');
        } catch (error) {
            console.error('SchemaProWP: Fel vid initiering av app:', error);
            
            // Visa felmeddelande i utvecklingsläge
            if (process.env.NODE_ENV === 'development') {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'schemaprowp-error';
                errorDiv.style.cssText = 'color: red; padding: 1em; border: 1px solid red; margin: 1em 0;';
                errorDiv.textContent = `Fel vid initiering av kalender: ${error.message}`;
                container.parentNode.insertBefore(errorDiv, container);
            }
        }
    });
}

// Initiera när DOM är redo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPublic);
} else {
    initPublic();
}

export default {};
