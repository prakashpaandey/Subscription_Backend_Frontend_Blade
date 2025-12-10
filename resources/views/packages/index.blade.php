<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Package Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .selected-Package {
            border-left: 4px solid #3b82f6;
            background-color: #f0f9ff;
        }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Package Management</h1>
            <p class="text-gray-600 mt-2">Manage user packages and their permissions across the platform</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
           
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-800">Packages</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="packages-table-body" class="bg-white divide-y divide-gray-100">
                                @forelse($packages as $package)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900">{{ $package->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $package->description ?? 'No description' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-wrap gap-1">
                                            @if($package->permissions->count() === $permissions->count())
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">All Permissions</span>
                                            @elseif($package->permissions->isEmpty())
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">No permissions</span>
                                            @else
                                                <div class="permission-badges" data-package-id="{{ $package->id }}">
                                                    @foreach($package->permissions->take(3) as $permission)
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full permission-badge">{{ $permission->name }}</span>
                                                    @endforeach
                                                    @if($package->permissions->count() > 3)
                                                       
                                                        <div class="hidden-permissions hidden inline">
                                                            @foreach($package->permissions->skip(3) as $permission)
                                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full permission-badge">{{ $permission->name }}</span>
                                                            @endforeach
                                                        </div>
                                                       
                                                        <button 
                                                            onclick="togglePermissions({{ $package->id }})"
                                                            class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full hover:bg-gray-200 cursor-pointer toggle-btn"
                                                        >
                                                            +{{ $package->permissions->count() - 3 }} more
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-500">{{ $package->updated_at->diffForHumans() }}</td>
                                    <td class="px-6 py-5">
                                        <button
                                            onclick="showEditForm({{ $package->id }})"
                                            class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2"
                                        >
                                            <i class="fas fa-edit text-sm"></i>
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No packages found. Create your first package!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div id="create-form" class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Create New Package</h3>
                    <p class="text-gray-500 text-sm mb-6">Add a new package with specific permissions</p>

                    <form id="create-package-form" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"> New Package Name</label>
                            <div class="relative">
                                <input
                                    id="create-package-name"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., Premium Package"
                                    required
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tag text-gray-400"></i>
                                </div>
                            </div>
                            <span id="create-name-error" class="text-red-600 text-sm mt-1 hidden block"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea
                                id="create-package-description"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                rows="3"
                                placeholder="Describe the package's purpose and responsibilities"
                            ></textarea>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Select Permissions</label>
                                <span id="create-permission-count" class="text-xs text-gray-500">0 selected</span>
                            </div>

                            <div class="border border-gray-300 rounded-xl p-4 h-64 overflow-y-auto">
                                <div id="create-permissions-container" class="space-y-3">
                                    @foreach($permissions as $permission)
                                    <div class="flex items-center">
                                        <input
                                            id="create-permission-{{ $permission->id }}"
                                            type="checkbox"
                                            data-permission-id="{{ $permission->id }}"
                                            class="create-permission-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                        />
                                        <label for="create-permission-{{ $permission->id }}" class="ml-3 text-gray-700">
                                            {{ $permission->name }} - <span class="text-gray-400">{{ $permission->description }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex gap-2 mt-4">
                                <button id="create-select-all" type="button" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                                <span class="text-gray-300">|</span>
                                <button id="create-clear-all" type="button" class="text-sm text-blue-600 hover:text-blue-800">Clear All</button>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium py-3.5 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-plus"></i>
                                Create Package
                            </button>
                            <button
                                id="create-cancel"
                                type="button"
                                class="w-full mt-3 border border-gray-300 text-gray-700 font-medium py-3 rounded-xl hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                
                <div id="edit-form" class="bg-white rounded-2xl shadow-lg p-6 sticky top-6 hidden">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Edit Package</h3>
                    <p class="text-gray-500 text-sm mb-6">Update package details and permissions</p>

                    <form id="edit-package-form" class="space-y-6">
                        @csrf
                        <input type="hidden" id="edit-package-id" />
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Package Name</label>
                            <div class="relative">
                                <input
                                    id="edit-package-name"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-tag text-gray-400"></i>
                                </div>
                            </div>
                            <span id="edit-name-error" class="text-red-600 text-sm mt-1 hidden block"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea
                                id="edit-package-description"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                rows="3"
                            ></textarea>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Select Permissions</label>
                                <span id="edit-permission-count" class="text-xs text-gray-500">0 selected</span>
                            </div>

                            <div class="border border-gray-300 rounded-xl p-4 h-64 overflow-y-auto">
                                <div id="edit-permissions-container" class="space-y-3">
                                    @foreach($permissions as $permission)
                                    <div class="flex items-center">
                                        <input
                                            id="edit-permission-{{ $permission->id }}"
                                            type="checkbox"
                                            data-permission-id="{{ $permission->id }}"
                                            class="edit-permission-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                        />
                                        <label for="edit-permission-{{ $permission->id }}" class="ml-3 text-gray-700">
                                            {{ $permission->name }} - <span class="text-gray-400">{{ $permission->description }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex gap-2 mt-4">
                                <button id="edit-select-all" type="button" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                                <span class="text-gray-300">|</span>
                                <button id="edit-clear-all" type="button" class="text-sm text-blue-600 hover:text-blue-800">Clear All</button>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium py-3.5 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-save"></i>
                                Save Changes
                            </button>
                            <button
                                id="edit-cancel"
                                type="button"
                                class="w-full mt-3 border border-gray-300 text-gray-700 font-medium py-3 rounded-xl hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

   
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <script>
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const apiBaseUrl = '/api';
        const packagesData = @json($packages);
        const permissionsData = @json($permissions);

        
        function togglePermissions(packageId) {
            const container = document.querySelector(`.permission-badges[data-package-id="${packageId}"]`);
            const hiddenPerms = container.querySelector('.hidden-permissions');
            const toggleBtn = container.querySelector('.toggle-btn');
            
            if (hiddenPerms.classList.contains('hidden')) {
                hiddenPerms.classList.remove('hidden');
                toggleBtn.textContent = 'See less';
            } else {
                hiddenPerms.classList.add('hidden');
                const count = hiddenPerms.querySelectorAll('.permission-badge').length;
                toggleBtn.textContent = `+${count} more`;
            }
        }

        
        async function showEditForm(packageId) {
            try {
                const response = await fetch(`${apiBaseUrl}/packages/${packageId}`);
                const package = await response.json();

                document.getElementById('edit-package-id').value = package.id;
                document.getElementById('edit-package-name').value = package.name;
                document.getElementById('edit-package-description').value = package.description || '';

                document.querySelectorAll('.edit-permission-checkbox').forEach(cb => cb.checked = false);

               
                package.permissions.forEach(perm => {
                    const checkbox = document.getElementById(`edit-permission-${perm.id}`);
                    if (checkbox) checkbox.checked = true;
                });

                updateEditPermissionCount();

               
                document.getElementById('create-form').classList.add('hidden');
                document.getElementById('edit-form').classList.remove('hidden');
            } catch (error) {
                showNotification('Failed to load package data', 'error');
            }
        }

        
        function showCreateForm() {
            document.getElementById('create-form').classList.remove('hidden');
            document.getElementById('edit-form').classList.add('hidden');
        }

       
        function updateCreatePermissionCount() {
            const count = document.querySelectorAll('.create-permission-checkbox:checked').length;
            document.getElementById('create-permission-count').textContent = `${count} selected`;
        }

        function updateEditPermissionCount() {
            const count = document.querySelectorAll('.edit-permission-checkbox:checked').length;
            document.getElementById('edit-permission-count').textContent = `${count} selected`;
        }

       
        document.getElementById('create-select-all').addEventListener('click', () => {
            document.querySelectorAll('.create-permission-checkbox').forEach(cb => cb.checked = true);
            updateCreatePermissionCount();
        });

        document.getElementById('create-clear-all').addEventListener('click', () => {
            document.querySelectorAll('.create-permission-checkbox').forEach(cb => cb.checked = false);
            updateCreatePermissionCount();
        });

        document.getElementById('edit-select-all').addEventListener('click', () => {
            document.querySelectorAll('.edit-permission-checkbox').forEach(cb => cb.checked = true);
            updateEditPermissionCount();
        });

        document.getElementById('edit-clear-all').addEventListener('click', () => {
            document.querySelectorAll('.edit-permission-checkbox').forEach(cb => cb.checked = false);
            updateEditPermissionCount();
        });

        document.querySelectorAll('.create-permission-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCreatePermissionCount);
        });

        document.querySelectorAll('.edit-permission-checkbox').forEach(cb => {
            cb.addEventListener('change', updateEditPermissionCount);
        });

        document.getElementById('create-package-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const nameInput = document.getElementById('create-package-name');
            const name = nameInput.value.trim();
            const description = document.getElementById('create-package-description').value.trim();
            const permissions = Array.from(document.querySelectorAll('.create-permission-checkbox:checked'))
                .map(cb => parseInt(cb.dataset.permissionId));

            document.getElementById('create-name-error').classList.add('hidden');
            nameInput.classList.remove('border-red-500');

            if (permissions.length === 0) {
                showNotification('Please select at least one permission', 'error');
                return;
            }

            try {
                const response = await fetch(`${apiBaseUrl}/packages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ name, description, permissions })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors && data.errors.name) {
                        const errorMsg = data.errors.name[0];
                        document.getElementById('create-name-error').textContent = errorMsg;
                        document.getElementById('create-name-error').classList.remove('hidden');
                        nameInput.classList.add('border-red-500');
                        showNotification(errorMsg, 'error');
                    } else {
                        throw new Error(data.message || 'Failed to create package');
                    }
                    return;
                }

                showNotification('Package created successfully!', 'success');
                document.getElementById('create-name-error').classList.add('hidden');
                nameInput.classList.remove('border-red-500');
                setTimeout(() => window.location.reload(), 1500);
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });

        
        document.getElementById('edit-package-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = document.getElementById('edit-package-id').value;
            const nameInput = document.getElementById('edit-package-name');
            const name = nameInput.value.trim();
            const description = document.getElementById('edit-package-description').value.trim();
            const permissions = Array.from(document.querySelectorAll('.edit-permission-checkbox:checked'))
                .map(cb => parseInt(cb.dataset.permissionId));

            document.getElementById('edit-name-error').classList.add('hidden');
            nameInput.classList.remove('border-red-500');

            if (permissions.length === 0) {
                showNotification('Please select at least one permission', 'error');
                return;
            }

            try {
                const response = await fetch(`${apiBaseUrl}/packages/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ name, description, permissions })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors && data.errors.name) {
                        const errorMsg = data.errors.name[0];
                        document.getElementById('edit-name-error').textContent = errorMsg;
                        document.getElementById('edit-name-error').classList.remove('hidden');
                        nameInput.classList.add('border-red-500');
                        showNotification(errorMsg, 'error');
                    } else {
                        throw new Error(data.message || 'Failed to update package');
                    }
                    return;
                }

                showNotification('Package updated successfully!', 'success');
                document.getElementById('edit-name-error').classList.add('hidden');
                nameInput.classList.remove('border-red-500');
                setTimeout(() => window.location.reload(), 1500);
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });

        document.getElementById('create-cancel').addEventListener('click', () => {
            document.getElementById('create-package-form').reset();
            updateCreatePermissionCount();
        });

        document.getElementById('edit-cancel').addEventListener('click', () => {
            showCreateForm();
        });

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `px-6 py-3 rounded-lg shadow-lg mb-2 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            notification.textContent = message;
            document.getElementById('toast-container').appendChild(notification);

            setTimeout(() => notification.remove(), 3000);
        }
    </script>
</body>
</html>
