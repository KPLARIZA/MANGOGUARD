// PWA Features
document.addEventListener('DOMContentLoaded', () => {
    // Request notification permission
    if ('Notification' in window) {
        const notifyBtn = document.getElementById('notifyBtn');
        if (notifyBtn) {
            notifyBtn.style.display = 'inline-block';
            notifyBtn.addEventListener('click', async () => {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    new Notification('MangoGuard', {
                        body: 'Notifications enabled! You will receive updates.',
                        icon: '/icons/icon-192x192.png'
                    });
                    notifyBtn.style.display = 'none';
                }
            });
        }
    }

    // Background Sync
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        const syncBtn = document.getElementById('syncBtn');
        if (syncBtn) {
            syncBtn.style.display = 'inline-block';
            syncBtn.addEventListener('click', async () => {
                try {
                    const registration = await navigator.serviceWorker.ready;
                    await registration.sync.register('sync-farm-images');
                    alert('Background sync registered! (Demo)');
                } catch (error) {
                    console.error('Background sync failed:', error);
                }
            });
        }
    }

    // Add to Home Screen
    let deferredPrompt;
    const addBtn = document.getElementById('addToHomeBtn');
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (addBtn) {
            addBtn.style.display = 'inline-block';
            addBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    deferredPrompt = null;
                    addBtn.style.display = 'none';
                }
            });
        }
    });
}); 