export const CONFIG = {
    selectors: {
        app: 'schemaprowp-app'
    },
    defaults: {
        isAdmin: false,
        wpData: {}
    },
    validation: {
        adminValues: ['1', 'true']
    }
};

export function waitForElement(selector) {
    return new Promise(resolve => {
        if (document.getElementById(selector)) {
            return resolve(document.getElementById(selector));
        }

        const observer = new MutationObserver(mutations => {
            if (document.getElementById(selector)) {
                observer.disconnect();
                resolve(document.getElementById(selector));
            }
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });
    });
}

export function validateAdminStatus(element) {
    if (!element) return CONFIG.defaults.isAdmin;
    
    const adminValue = element.dataset.isAdmin;
    if (adminValue === undefined) {
        console.warn('data-is-admin attribute missing, defaulting to false');
        return CONFIG.defaults.isAdmin;
    }
    
    return CONFIG.validation.adminValues.includes(adminValue);
}
