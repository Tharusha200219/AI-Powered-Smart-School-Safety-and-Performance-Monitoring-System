{{-- Beautiful Notification System --}}
<<<<<<< HEAD
<style>
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
        width: 100%;
    }

    .notification {
        display: flex;
        align-items: flex-start;
        padding: 16px 20px;
        margin-bottom: 12px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(100%);
        opacity: 0;
        animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .notification:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .notification.removing {
        animation: slideOutRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Success Notification */
    .notification.success {
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        color: white;
    }

    .notification.success .notification-icon {
        color: #dcfce7;
    }

    /* Error/Danger Notification */
    .notification.danger,
    .notification.error {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        color: white;
    }

    .notification.danger .notification-icon,
    .notification.error .notification-icon {
        color: #fecaca;
    }

    /* Warning Notification */
    .notification.warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }

    .notification.warning .notification-icon {
        color: #fef3c7;
    }

    /* Info Notification */
    .notification.info {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        color: white;
    }

    .notification.info .notification-icon {
        color: #dbeafe;
    }

    .notification-icon {
        flex-shrink: 0;
        margin-right: 12px;
        font-size: 24px;
        margin-top: 2px;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 2px;
    }

    .notification-message {
        font-size: 13px;
        line-height: 1.4;
        opacity: 0.9;
    }

    .notification-close {
        flex-shrink: 0;
        background: none;
        border: none;
        color: inherit;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        margin-left: 12px;
        opacity: 0.7;
        transition: opacity 0.2s;
        margin-top: 2px;
    }

    .notification-close:hover {
        opacity: 1;
    }

    .notification-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        transition: width linear;
        border-radius: 0 0 12px 12px;
    }

    .notification.success .notification-progress {
        background: rgba(255, 255, 255, 0.4);
    }

    .notification.danger .notification-progress,
    .notification.error .notification-progress {
        background: rgba(255, 255, 255, 0.4);
    }

    .notification.warning .notification-progress {
        background: rgba(255, 255, 255, 0.4);
    }

    .notification.info .notification-progress {
        background: rgba(255, 255, 255, 0.4);
    }

    /* Mobile Responsive */
    @media (max-width: 640px) {
        .notification-container {
            right: 10px;
            left: 10px;
            max-width: none;
        }

        .notification {
            padding: 14px 16px;
            margin-bottom: 10px;
        }

        .notification-title {
            font-size: 13px;
        }

        .notification-message {
            font-size: 12px;
        }
    }

    /* Material Design Icons */
    .material-symbols-outlined {
        font-variation-settings:
            'FILL' 1,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
    }
</style>
=======
@vite('resources/css/components/notifications.css')
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606

{{-- Material Symbols Font --}}
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

<div class="notification-container" id="notificationContainer">
    {{-- Notifications will be dynamically added here --}}
</div>

<<<<<<< HEAD
<script>
    class NotificationManager {
        constructor() {
            this.container = document.getElementById('notificationContainer');
            this.notifications = new Map();
            this.defaultDuration = 5000; // 5 seconds
        }

        show(type, title, message, duration = null) {
            const id = this.generateId();
            const notification = this.createNotification(id, type, title, message, duration || this
            .defaultDuration);

            this.container.appendChild(notification);
            this.notifications.set(id, notification);

            // Start auto-dismiss timer
            this.startAutoDismiss(id, duration || this.defaultDuration);

            return id;
        }

        createNotification(id, type, title, message, duration) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.setAttribute('data-id', id);

            const icon = this.getIcon(type);

            notification.innerHTML = `
            <span class="notification-icon material-symbols-outlined">${icon}</span>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close material-symbols-outlined" onclick="notificationManager.dismiss('${id}')">close</button>
            <div class="notification-progress" style="width: 100%"></div>
        `;

            return notification;
        }

        getIcon(type) {
            const icons = {
                success: 'check_circle',
                error: 'error',
                danger: 'error',
                warning: 'warning',
                info: 'info'
            };
            return icons[type] || 'notifications';
        }

        startAutoDismiss(id, duration) {
            const notification = this.notifications.get(id);
            if (!notification) return;

            const progressBar = notification.querySelector('.notification-progress');

            // Animate progress bar
            progressBar.style.transition = `width ${duration}ms linear`;
            progressBar.style.width = '0%';

            // Auto dismiss
            setTimeout(() => {
                this.dismiss(id);
            }, duration);
        }

        dismiss(id) {
            const notification = this.notifications.get(id);
            if (!notification) return;

            notification.classList.add('removing');

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                this.notifications.delete(id);
            }, 300);
        }

        dismissAll() {
            this.notifications.forEach((_, id) => {
                this.dismiss(id);
            });
        }

        generateId() {
            return 'notification_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        // Convenience methods
        success(title, message, duration) {
            return this.show('success', title, message, duration);
        }

        error(title, message, duration) {
            return this.show('error', title, message, duration);
        }

        warning(title, message, duration) {
            return this.show('warning', title, message, duration);
        }

        info(title, message, duration) {
            return this.show('info', title, message, duration);
        }
    }

    // Global instance
    window.notificationManager = new NotificationManager();

=======
@vite('resources/js/components/notifications.js')

<script>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
    // Laravel Flash Message Integration
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            notificationManager.success('Success', '{{ session('success') }}');
        @endif

        @if (session('error'))
            notificationManager.error('Error', '{{ session('error') }}');
        @endif

        @if (session('warning'))
            notificationManager.warning('Warning', '{{ session('warning') }}');
        @endif

        @if (session('info'))
            notificationManager.info('Information', '{{ session('info') }}');
        @endif

        @if ($errors->any())
            notificationManager.error('Validation Error', '{{ $errors->first() }}');
        @endif
    });
</script>
