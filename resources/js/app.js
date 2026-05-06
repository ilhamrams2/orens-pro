import './bootstrap';

window.showToast = function(type, title, message) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-6 right-6 z-[200] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    const colorClass = type === 'success' ? 'border-green-100 bg-green-50 text-green-800' : 'border-red-100 bg-red-50 text-red-800';
    const icon = type === 'success' ? 
        '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
        '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

    toast.className = `flex items-center gap-4 p-5 pr-8 rounded-2xl border ${colorClass} shadow-xl animate-slide-in pointer-events-auto backdrop-blur-md bg-opacity-90`;
    toast.innerHTML = `
        <div class="flex-shrink-0 w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">${icon}</div>
        <div>
            <p class="text-sm font-black tracking-tight">${title}</p>
            <p class="text-xs opacity-80 font-medium">${message}</p>
        </div>
    `;

    document.getElementById('toast-container').appendChild(toast);

    setTimeout(() => {
        toast.classList.add('animate-slide-out');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
};

// CSS for toasts
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in {
        from { transform: translateX(100%) scale(0.9); opacity: 0; }
        to { transform: translateX(0) scale(1); opacity: 1; }
    }
    @keyframes slide-out {
        from { transform: translateX(0) scale(1); opacity: 1; }
        to { transform: translateX(100%) scale(0.9); opacity: 0; }
    }
    .animate-slide-in { animation: slide-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-slide-out { animation: slide-out 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
`;
document.head.appendChild(style);
