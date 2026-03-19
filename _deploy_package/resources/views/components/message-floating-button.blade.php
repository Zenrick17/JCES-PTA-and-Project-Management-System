<!-- Message Floating Action Button & Popup -->
<div x-data="messagePopup()" x-cloak>
    <!-- Floating Action Button -->
    <button @click="open = !open"
            class="fixed bottom-6 right-6 w-14 h-14 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 z-50"
            :class="open ? 'rotate-45 bg-red-500 hover:bg-red-600' : ''">
        <svg x-show="!open" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                  clip-rule="evenodd"/>
        </svg>
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <!-- Message Popup Card -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-24 right-6 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 overflow-hidden">

        <!-- Popup Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">Create Announcement</h3>
                    <p class="text-green-100 text-xs">Share important information</p>
                </div>
            </div>
        </div>

        <!-- Popup Body / Form -->
        <form @submit.prevent="sendMessage()" class="p-5 space-y-4">
            <!-- Type Selection -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                <select x-model="form.type"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    <option value="announcement">Announcement</option>
                    <option value="schedule">Schedule</option>
                </select>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Title</label>
                <input type="text" x-model="form.title" placeholder="Enter title..."
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                <p x-show="errors.title" x-text="errors.title" class="text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Announcement Fields -->
            <div x-show="form.type === 'announcement'">
                <!-- Category -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                    <select x-model="form.category"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="">Select category...</option>
                        <option value="important">Important</option>
                        <option value="notice">Notice</option>
                        <option value="update">Update</option>
                        <option value="event">Event</option>
                    </select>
                    <p x-show="errors.category" x-text="errors.category" class="text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Audience -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Audience</label>
                    <select x-model="form.audience"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="">Select audience...</option>
                        <option value="everyone">Everyone</option>
                        <option value="parents">Parents</option>
                        <option value="teachers">Teachers</option>
                        <option value="administrator">Administrators</option>
                        <option value="principal">Principals</option>
                        <option value="supporting_staff">Supporting Staff (Admin & Principal)</option>
                        <option value="faculty">Faculty (Principal, Teachers & Admin)</option>
                    </select>
                    <p x-show="errors.audience" x-text="errors.audience" class="text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Schedule Fields -->
            <div x-show="form.type === 'schedule'">
                <!-- Date -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Date</label>
                    <input type="date" x-model="form.scheduled_date"
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    <p x-show="errors.scheduled_date" x-text="errors.scheduled_date" class="text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Time -->
                <div class="mb-4 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start Time</label>
                        <input type="time" x-model="form.start_time"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">End Time</label>
                        <input type="time" x-model="form.end_time"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    </div>
                </div>

                <!-- Priority -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Priority</label>
                    <select x-model="form.priority"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="">Select priority...</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <p x-show="errors.priority" x-text="errors.priority" class="text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Visibility -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Visibility</label>
                    <select x-model="form.visibility"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="">Select visibility...</option>
                        <option value="everyone">Everyone</option>
                        <option value="administrator">Administrators</option>
                        <option value="principal">Principals</option>
                        <option value="teacher">Teachers</option>
                        <option value="parent">Parents</option>
                    </select>
                    <p x-show="errors.visibility" x-text="errors.visibility" class="text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Content/Description -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1" x-text="form.type === 'schedule' ? 'Description' : 'Content'"></label>
                <textarea x-model="form.description" rows="3" :placeholder="form.type === 'schedule' ? 'Type schedule description...' : 'Type announcement content...'"
                          class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition resize-none"></textarea>
                <p x-show="errors.description" x-text="errors.description" class="text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Send Button -->
            <button type="submit"
                    :disabled="sending"
                    class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg x-show="!sending" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg x-show="sending" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="sending ? 'Publishing...' : (form.type === 'schedule' ? 'Create Schedule' : 'Publish Announcement')"></span>
            </button>
        </form>
    </div>

    <!-- Toast Notification (Bottom Right) -->
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="fixed right-6 z-[60] max-w-sm w-80 rounded-xl shadow-2xl border overflow-hidden"
             :class="{
                 'bg-green-50 border-green-200': toast.type === 'success',
                 'bg-red-50 border-red-200': toast.type === 'error',
                 'bg-yellow-50 border-yellow-200': toast.type === 'warning'
             }"
             :style="'bottom: ' + (6 + index * 5) + 'rem'">
            <div class="flex items-start gap-3 p-4">
                <!-- Success Icon -->
                <div x-show="toast.type === 'success'" class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <!-- Error Icon -->
                <div x-show="toast.type === 'error'" class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <!-- Warning Icon -->
                <div x-show="toast.type === 'warning'" class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm"
                       :class="{
                           'text-green-900': toast.type === 'success',
                           'text-red-900': toast.type === 'error',
                           'text-yellow-900': toast.type === 'warning'
                       }"
                       x-text="toast.title"></p>
                    <p class="text-xs mt-1"
                       :class="{
                           'text-green-700': toast.type === 'success',
                           'text-red-700': toast.type === 'error',
                           'text-yellow-700': toast.type === 'warning'
                       }"
                       x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <!-- Progress Bar -->
            <div class="h-1"
                 :class="{
                     'bg-green-200': toast.type === 'success',
                     'bg-red-200': toast.type === 'error',
                     'bg-yellow-200': toast.type === 'warning'
                 }">
                <div class="h-full transition-all duration-100"
                     :class="{
                         'bg-green-500': toast.type === 'success',
                         'bg-red-500': toast.type === 'error',
                         'bg-yellow-500': toast.type === 'warning'
                     }"
                     :style="'width: ' + toast.progress + '%'"></div>
            </div>
        </div>
    </template>
</div>

<script>
function messagePopup() {
    return {
        open: false,
        sending: false,
        form: {
            type: 'announcement',
            title: '',
            category: '',
            audience: '',
            description: '',
            scheduled_date: '',
            start_time: '',
            end_time: '',
            priority: '',
            visibility: ''
        },
        errors: {},
        toasts: [],
        toastCounter: 0,

        validate() {
            this.errors = {};
            let valid = true;

            if (!this.form.title.trim()) {
                this.errors.title = 'Title is required.';
                valid = false;
            }
            if (!this.form.description.trim()) {
                this.errors.description = 'Description is required.';
                valid = false;
            }

            if (this.form.type === 'announcement') {
                if (!this.form.category) {
                    this.errors.category = 'Please select a category.';
                    valid = false;
                }
                if (!this.form.audience) {
                    this.errors.audience = 'Please select an audience.';
                    valid = false;
                }
            } else if (this.form.type === 'schedule') {
                if (!this.form.scheduled_date) {
                    this.errors.scheduled_date = 'Date is required.';
                    valid = false;
                }
                if (!this.form.priority) {
                    this.errors.priority = 'Please select a priority.';
                    valid = false;
                }
                if (!this.form.visibility) {
                    this.errors.visibility = 'Please select visibility.';
                    valid = false;
                }
            }

            return valid;
        },

        sendMessage() {
            if (!this.validate()) {
                this.addToast('warning', 'Incomplete Form', 'Please fill in all required fields.');
                return;
            }

            this.sending = true;

            // Prepare data based on type
            let url, data;
            if (this.form.type === 'announcement') {
                url = '{{ route('administrator.announcements.store') }}';
                data = {
                    title: this.form.title,
                    content: this.form.description,
                    category: this.form.category,
                    audience: this.form.audience
                };
            } else {
                url = '{{ route('administrator.schedules.store') }}';
                data = {
                    title: this.form.title,
                    description: this.form.description,
                    scheduled_date: this.form.scheduled_date,
                    start_time: this.form.start_time,
                    end_time: this.form.end_time,
                    priority: this.form.priority,
                    visibility: this.form.visibility
                };
            }

            // Send data via AJAX
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                this.sending = false;
                if (data.success) {
                    const successMsg = this.form.type === 'schedule' ? 'Schedule created successfully!' : 'Announcement published successfully!';
                    this.addToast('success', 'Success!', successMsg);
                    this.resetForm();
                    this.open = false;
                    // Reload page to show new announcement/schedule
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.addToast('error', 'Error', data.message || 'Operation failed.');
                }
            })
            .catch(error => {
                this.sending = false;
                this.addToast('error', 'Error', 'An error occurred. Please try again.');
                console.error('Error:', error);
            });
        },

        resetForm() {
            this.form = {
                type: 'announcement',
                title: '',
                category: '',
                audience: '',
                description: '',
                scheduled_date: '',
                start_time: '',
                end_time: '',
                priority: '',
                visibility: ''
            };
            this.errors = {};
        },

        addToast(type, title, message) {
            const id = ++this.toastCounter;
            const toast = { id, type, title, message, visible: true, progress: 100 };
            this.toasts.push(toast);

            // Animate the progress bar
            const duration = 4000;
            const interval = 50;
            const steps = duration / interval;
            const decrement = 100 / steps;
            let remaining = 100;

            const timer = setInterval(() => {
                remaining -= decrement;
                const t = this.toasts.find(t => t.id === id);
                if (t) {
                    t.progress = Math.max(0, remaining);
                    if (remaining <= 0) {
                        clearInterval(timer);
                        this.removeToast(id);
                    }
                } else {
                    clearInterval(timer);
                }
            }, interval);
        },

        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    };
}
</script>
