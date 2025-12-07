<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="py-6">
                @yield('content')
            </main>
        </div>

        <!-- Modal Konfirmasi Penghapusan -->
        <div id="deleteConfirmModal" 
             class="fixed inset-0 flex items-center justify-center z-50 transition-opacity duration-300" 
             style="display: none; z-index: 99999; background-color: rgba(0, 0, 0, 0.5);"
             onclick="closeDeleteModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 transform transition-all duration-300 relative max-h-[85vh] overflow-y-auto" 
                 style="max-width: 340px; z-index: 100000;" 
                 onclick="event.stopPropagation()">
                <div class="p-6">
                    <!-- Icon Warning -->
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4">
                        <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 22h20L12 2zm0 3.99L19.53 20H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                        </svg>
                    </div>
                    
                    <!-- Judul -->
                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
                        Konfirmasi Penghapusan
                    </h3>
                    
                    <!-- Subjudul -->
                    <p class="text-sm text-gray-600 text-center mb-6 leading-relaxed" id="deleteConfirmMessage">
                        Yakin ingin menghapus data ini?
                    </p>
                    
                    <!-- Tombol -->
                    <div class="flex items-center gap-3 mt-6">
                        <button type="button" 
                                onclick="closeDeleteModal()"
                                class="flex-1 px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 shadow-sm">
                            Batal
                        </button>
                        <form id="deleteConfirmForm" method="POST" class="flex-1 m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Kirim Reminder -->
        <div id="remindConfirmModal" 
             class="fixed inset-0 flex items-center justify-center z-50 transition-opacity duration-300" 
             style="display: none; z-index: 99999; background-color: rgba(0, 0, 0, 0.5);"
             onclick="closeRemindModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 transform transition-all duration-300 relative max-h-[85vh] overflow-y-auto" 
                 style="max-width: 340px; z-index: 100000;" 
                 onclick="event.stopPropagation()">
                <div class="p-6">
                    <!-- Icon Warning -->
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4">
                        <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 22h20L12 2zm0 3.99L19.53 20H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                        </svg>
                    </div>
                    
                    <!-- Judul -->
                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
                        Konfirmasi Kirim Reminder
                    </h3>
                    
                    <!-- Subjudul -->
                    <p class="text-sm text-gray-600 text-center mb-6 leading-relaxed" id="remindConfirmMessage">
                        Yakin ingin mengirim reminder pembayaran ke client?
                    </p>
                    
                    <!-- Tombol -->
                    <div class="flex items-center gap-3 mt-6">
                        <button type="button" 
                                onclick="closeRemindModal()"
                                class="flex-1 px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 shadow-sm">
                            Batal
                        </button>
                        <form id="remindConfirmForm" method="POST" class="flex-1 m-0">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Ya, Kirim
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Persetujuan -->
        <div id="approveConfirmModal" 
             class="fixed inset-0 flex items-center justify-center z-50 transition-opacity duration-300" 
             style="display: none; z-index: 99999; background-color: rgba(0, 0, 0, 0.5);"
             onclick="closeApproveModal()">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 transform transition-all duration-300 relative max-h-[85vh] overflow-y-auto" 
                 style="max-width: 340px; z-index: 100000;" 
                 onclick="event.stopPropagation()">
                <div class="p-6">
                    <!-- Icon Warning -->
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4">
                        <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 22h20L12 2zm0 3.99L19.53 20H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                        </svg>
                    </div>
                    
                    <!-- Judul -->
                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
                        Konfirmasi Persetujuan
                    </h3>
                    
                    <!-- Subjudul -->
                    <p class="text-sm text-gray-600 text-center mb-6 leading-relaxed" id="approveConfirmMessage">
                        Yakin ingin menyetujui pesanan ini?
                    </p>
                    
                    <!-- Tombol -->
                    <div class="flex items-center gap-3 mt-6">
                        <button type="button" 
                                onclick="closeApproveModal()"
                                class="flex-1 px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 shadow-sm">
                            Batal
                        </button>
                        <form id="approveConfirmForm" method="POST" class="flex-1 m-0">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Ya, Setujui
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Universal Modal (Confirm, Alert, Info, Error) - Keep for backward compatibility -->
        <div id="universalModal" class="fixed inset-0 flex items-center justify-center z-50 transition-opacity duration-300" style="display: none; z-index: 99999; background-color: rgba(0, 0, 0, 0.5);">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-all duration-300 relative max-h-[400px] overflow-y-auto" style="max-width: 400px; z-index: 100000;" onclick="event.stopPropagation()">
                <div class="py-6 px-8">
                    <div id="modalIcon" class="flex items-center justify-center mx-auto mt-2 mb-3">
                        <!-- Icon akan diisi oleh JavaScript -->
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 text-center mb-2" id="modalTitle">Title</h3>
                    <p class="text-sm text-gray-600 text-center leading-relaxed" id="modalMessage">Message</p>
                    <div id="modalButtons" class="flex gap-3 mt-4">
                        <!-- Buttons akan diisi oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function() {
            let confirmCallback = null;
            let confirmForm = null;

            // Icon configurations
            const iconConfigs = {
                confirm: {
                    bg: 'bg-yellow-100',
                    icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    color: 'text-yellow-600'
                },
                alert: {
                    bg: 'bg-blue-100',
                    icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-blue-600'
                },
                error: {
                    bg: 'bg-red-100',
                    icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-red-600'
                },
                warning: {
                    bg: 'bg-orange-100',
                    icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    color: 'text-orange-600'
                },
                info: {
                    bg: 'bg-blue-100',
                    icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-blue-600'
                },
                success: {
                    bg: 'bg-green-100',
                    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-green-600'
                }
            };

            function showUniversalModal(type, title, message, options = {}) {
                const modal = document.getElementById('universalModal');
                const iconEl = document.getElementById('modalIcon');
                const titleEl = document.getElementById('modalTitle');
                const messageEl = document.getElementById('modalMessage');
                const buttonsEl = document.getElementById('modalButtons');
                
                if (!modal) {
                    console.error('Modal not found!');
                    return false;
                }

                // Set icon
                const config = iconConfigs[type] || iconConfigs.alert;
                if (type === 'confirm') {
                    // For confirm type, use yellow warning triangle - 70px (w-[70px] h-[70px])
                    iconEl.className = `flex items-center justify-center mx-auto mt-2 mb-3`;
                    iconEl.innerHTML = `<svg class="w-[70px] h-[70px] text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 22h20L12 2zm0 3.99L19.53 20H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                    </svg>`;
                } else {
                    iconEl.className = `flex items-center justify-center w-16 h-16 mx-auto mt-2 mb-3 ${config.bg} rounded-full`;
                    iconEl.innerHTML = `<svg class="w-8 h-8 ${config.color}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${config.icon}"></path>
                </svg>`;
                }

                // Set title and message
                if (titleEl) {
                    titleEl.textContent = title || 'Notifikasi';
                    titleEl.className = 'text-base font-semibold text-gray-900 text-center mb-2';
                }
                if (messageEl) {
                    messageEl.textContent = message || '';
                    messageEl.className = 'text-sm text-gray-600 text-center leading-relaxed';
                }

                // Store callbacks and form before clearing buttons
                const callback = options.callback || null;
                const cancelCallback = options.cancelCallback || null;
                confirmForm = options.form || null;

                // Set buttons based on type
                buttonsEl.innerHTML = '';
                
                if (type === 'confirm') {
                    // Confirm modal: Batal and Ya, Hapus button
                    const cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'flex-1 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-md';
                    cancelBtn.style.cssText = 'background-color: #6b7280 !important; color: #ffffff !important; border: none !important; cursor: pointer;';
                    cancelBtn.textContent = options.cancelText || 'Batal';
                    cancelBtn.onmouseenter = function() {
                        this.style.backgroundColor = '#4b5563';
                        this.style.transform = 'translateY(-1px)';
                    };
                    cancelBtn.onmouseleave = function() {
                        this.style.backgroundColor = '#6b7280';
                        this.style.transform = 'translateY(0)';
                    };
                    cancelBtn.onclick = function() {
                        if (cancelCallback) cancelCallback();
                        closeUniversalModal();
                    };
                    
                    const confirmBtn = document.createElement('button');
                    confirmBtn.type = 'button';
                    confirmBtn.className = 'flex-1 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-md';
                    // Use red for delete action, or blue if specified
                    const confirmColor = options.confirmColor === 'blue' ? '#2563eb' : '#dc2626';
                    const confirmHoverColor = options.confirmColor === 'blue' ? '#1d4ed8' : '#b91c1c';
                    confirmBtn.style.cssText = `background-color: ${confirmColor} !important; color: #ffffff !important; border: none !important; cursor: pointer;`;
                    confirmBtn.textContent = options.confirmText || 'Ya, Hapus';
                    confirmBtn.onmouseenter = function() {
                        this.style.backgroundColor = confirmHoverColor;
                        this.style.transform = 'translateY(-1px)';
                    };
                    confirmBtn.onmouseleave = function() {
                        this.style.backgroundColor = confirmColor;
                        this.style.transform = 'translateY(0)';
                    };
                    confirmBtn.onclick = function() {
                        if (callback) callback();
                        if (confirmForm) confirmForm.submit();
                        closeUniversalModal();
                    };
                    
                    buttonsEl.appendChild(cancelBtn);
                    buttonsEl.appendChild(confirmBtn);
                    confirmCallback = callback;
                } else {
                    // Alert modal: single OK button
                    const okBtn = document.createElement('button');
                    okBtn.type = 'button';
                    okBtn.className = 'w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-md';
                    okBtn.style.cssText = 'background-color: #2563eb !important; color: #ffffff !important; border: none !important; cursor: pointer;';
                    okBtn.textContent = options.okText || 'OK';
                    okBtn.onmouseenter = function() {
                        this.style.backgroundColor = '#1d4ed8';
                        this.style.transform = 'translateY(-1px)';
                    };
                    okBtn.onmouseleave = function() {
                        this.style.backgroundColor = '#2563eb';
                        this.style.transform = 'translateY(0)';
                    };
                    okBtn.onclick = function() {
                        if (callback) callback();
                        closeUniversalModal();
                    };
                    buttonsEl.appendChild(okBtn);
                    confirmCallback = null;
                }
                
                // Show modal with animation
                modal.style.display = 'flex';
                modal.style.zIndex = '99999';
                modal.style.position = 'fixed';
                document.body.style.overflow = 'hidden';
                
                // Set initial state for animation
                modal.style.opacity = '0';
                const modalContent = modal.querySelector('div[onclick]');
                if (modalContent) {
                    modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                    modalContent.style.opacity = '0';
                    modalContent.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                }
                
                // Animate modal appearance
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        modal.style.opacity = '1';
                        if (modalContent) {
                            modalContent.style.transform = 'scale(1) translateY(0)';
                            modalContent.style.opacity = '1';
                        }
                    }, 10);
                });
                
                return true;
            }

            function closeUniversalModal() {
                const modal = document.getElementById('universalModal');
                if (modal) {
                    // Animate modal disappearance
                    modal.style.opacity = '0';
                    const modalContent = modal.querySelector('div[onclick]');
                    if (modalContent) {
                        modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                        modalContent.style.opacity = '0';
                    }
                    
                    setTimeout(() => {
                    modal.style.display = 'none';
                        document.body.style.overflow = '';
                    }, 300);
                } else {
                    document.body.style.overflow = '';
                }
                confirmCallback = null;
                confirmForm = null;
            }

            // Make functions global
            window.showUniversalModal = showUniversalModal;
            window.closeUniversalModal = closeUniversalModal;

            // Backward compatibility
            function showConfirmModal(title, message, form) {
                return showUniversalModal('confirm', title, message, { form: form });
            }

            window.showConfirmModal = showConfirmModal;

            // Custom alert function (non-blocking)
            window.showAlert = function(message, type = 'alert') {
                showUniversalModal(type, 'Notifikasi', message);
            };

            // Custom confirm function (callback-based)
            window.showConfirm = function(title, message, onConfirm, onCancel) {
                showUniversalModal('confirm', title || 'Konfirmasi', message, {
                    callback: onConfirm || function() {},
                    cancelCallback: onCancel || function() {}
                });
            };

            // Function to confirm delete
            function confirmDelete(formId, message) {
                const form = document.getElementById(formId);
                if (!form) {
                    console.error('Form not found:', formId);
                    return;
                }
                showConfirmModal('Konfirmasi Hapus', message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.', form);
            }
            
            // Make it global
            window.confirmDelete = confirmDelete;

            // Initialize delete buttons - Simple and direct approach
            function initDeleteButtons() {
                // Handle forms with class delete-form
                document.querySelectorAll('form.delete-form .delete-btn').forEach(function(button) {
                    // Remove existing listeners to avoid duplicates
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    
                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const form = newButton.closest('form');
                        if (!form) {
                            console.error('Form not found');
                            return;
                        }
                        
                        const message = form.getAttribute('data-message') || 'Apakah Anda yakin ingin menghapus data ini?';
                        
                        if (window.showConfirmModal) {
                            window.showConfirmModal('Konfirmasi Hapus', message, form);
                        }
                    });
                });
                
                // Handle forms with onsubmit containing confirm
                document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function(form) {
                    if (form.classList.contains('delete-form')) return; // Skip already handled
                    
                    const onsubmit = form.getAttribute('onsubmit');
                    if (onsubmit && onsubmit.includes('confirm')) {
                        // Extract message from confirm
                        const match = onsubmit.match(/confirm\(['"]([^'"]+)['"]\)/);
                        const message = match ? match[1] : 'Apakah Anda yakin ingin menghapus data ini?';
                        
                        // Remove onsubmit attribute
                        form.removeAttribute('onsubmit');
                        
                        // Find submit button and change to button type
                        const submitButton = form.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.type = 'button';
                            submitButton.classList.add('delete-btn');
                            form.classList.add('delete-form');
                            form.setAttribute('data-message', message);
                            
                            submitButton.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                if (window.showConfirmModal) {
                                    window.showConfirmModal('Konfirmasi Hapus', message, form);
                                }
                            });
                        }
                    }
                });
            }

            // Initialize multiple times to catch dynamic content
            function runInit() {
                initDeleteButtons();
            }

            // Run on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', runInit);
            } else {
                runInit();
            }
            
            // Also run after a short delay to catch any late-loading content
            setTimeout(runInit, 500);

            // Close modal when clicking outside
            setTimeout(function() {
                const modal = document.getElementById('universalModal');
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeUniversalModal();
                        }
                    });
                }
            }, 100);

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeUniversalModal();
                }
            });
        })();

        // Modal Konfirmasi Penghapusan - Clean & Simple
        function openDeleteModal(deleteUrl, itemName = '') {
            const modal = document.getElementById('deleteConfirmModal');
            const form = document.getElementById('deleteConfirmForm');
            const messageEl = document.getElementById('deleteConfirmMessage');
            
            if (!modal || !form) {
                console.error('Modal or form not found');
                return;
            }
            
            // Set form action
            form.action = deleteUrl;
            
            // Set message with item name if provided
            if (messageEl && itemName) {
                messageEl.textContent = `Yakin ingin menghapus ${itemName}?`;
            } else if (messageEl) {
                messageEl.textContent = 'Yakin ingin menghapus data ini?';
            }
            
            // Show modal with animation
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            document.body.style.overflow = 'hidden';
            
            const modalContent = modal.querySelector('div[onclick]');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                modalContent.style.opacity = '0';
            }
            
            // Animate in
            requestAnimationFrame(() => {
                setTimeout(() => {
                    modal.style.opacity = '1';
                    if (modalContent) {
                        modalContent.style.transform = 'scale(1) translateY(0)';
                        modalContent.style.opacity = '1';
                    }
                }, 10);
            });
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmModal');
            if (!modal) return;
            
            // Animate out
            modal.style.opacity = '0';
            const modalContent = modal.querySelector('div[onclick]');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                modalContent.style.opacity = '0';
            }
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        // Modal Konfirmasi Kirim Reminder - Clean & Simple
        function openRemindModal(remindUrl, itemName = '') {
            const modal = document.getElementById('remindConfirmModal');
            const form = document.getElementById('remindConfirmForm');
            const messageEl = document.getElementById('remindConfirmMessage');
            
            if (!modal || !form) {
                console.error('Modal or form not found');
                return;
            }
            
            // Set form action
            form.action = remindUrl;
            
            // Set message with item name if provided
            if (messageEl && itemName) {
                messageEl.textContent = `Yakin ingin mengirim reminder pembayaran untuk ${itemName}?`;
            } else if (messageEl) {
                messageEl.textContent = 'Yakin ingin mengirim reminder pembayaran ke client?';
            }
            
            // Show modal with animation
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            document.body.style.overflow = 'hidden';
            
            const modalContent = modal.querySelector('div[onclick]');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                modalContent.style.opacity = '0';
            }
            
            // Animate in
            requestAnimationFrame(() => {
                setTimeout(() => {
                    modal.style.opacity = '1';
                    if (modalContent) {
                        modalContent.style.transform = 'scale(1) translateY(0)';
                        modalContent.style.opacity = '1';
                    }
                }, 10);
            });
        }

        function closeRemindModal() {
            const modal = document.getElementById('remindConfirmModal');
            if (!modal) return;
            
            // Animate out
            modal.style.opacity = '0';
            const modalContent = modal.querySelector('div[onclick]');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                modalContent.style.opacity = '0';
            }
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        // Modal Konfirmasi Persetujuan - Clean & Simple
        function openApproveModal(approveUrl, itemName = '') {
            const modal = document.getElementById('approveConfirmModal');
            const form = document.getElementById('approveConfirmForm');
            const messageEl = document.getElementById('approveConfirmMessage');
            
            if (!modal || !form) {
                console.error('Modal or form not found');
                return;
            }
            
            // Set form action
            form.action = approveUrl;
            
            // Set message with item name if provided
            if (messageEl && itemName) {
                messageEl.textContent = `Yakin ingin menyetujui ${itemName}?`;
            } else if (messageEl) {
                messageEl.textContent = 'Yakin ingin menyetujui pesanan ini?';
            }
            
            // Show modal with animation
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            document.body.style.overflow = 'hidden';
            
            const modalContent = modal.querySelector('div[onclick]');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                modalContent.style.opacity = '0';
            }
            
            // Animate in
            requestAnimationFrame(() => {
                setTimeout(() => {
                    modal.style.opacity = '1';
                    if (modalContent) {
                        modalContent.style.transform = 'scale(1) translateY(0)';
                        modalContent.style.opacity = '1';
                    }
                }, 10);
            });
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveConfirmModal');
            if (!modal) return;
            
            // Animate out
            modal.style.opacity = '0';
            const modalContent = modal.querySelector('div[onclick]');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95) translateY(-10px)';
                modalContent.style.opacity = '0';
            }
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const deleteModal = document.getElementById('deleteConfirmModal');
                const remindModal = document.getElementById('remindConfirmModal');
                const approveModal = document.getElementById('approveConfirmModal');
                if (deleteModal && deleteModal.style.display === 'flex') {
                    closeDeleteModal();
                } else if (remindModal && remindModal.style.display === 'flex') {
                    closeRemindModal();
                } else if (approveModal && approveModal.style.display === 'flex') {
                    closeApproveModal();
                } else {
                    closeUniversalModal();
                }
            }
        });

        // Make functions global
        window.openDeleteModal = openDeleteModal;
        window.closeDeleteModal = closeDeleteModal;
        window.openRemindModal = openRemindModal;
        window.closeRemindModal = closeRemindModal;
        window.openApproveModal = openApproveModal;
        window.closeApproveModal = closeApproveModal;
        </script>
    </body>
</html>
