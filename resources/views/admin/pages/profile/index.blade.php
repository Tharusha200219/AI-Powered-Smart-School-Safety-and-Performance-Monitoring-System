@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-page {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .profile-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #45b7d1);
            background-size: 400% 400%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .profile-avatar {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .profile-avatar:hover img {
            transform: scale(1.05);
        }

        .profile-avatar .edit-avatar {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .profile-avatar .edit-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .profile-info {
            text-align: center;
        }

        .profile-name {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .profile-role {
            font-size: 16px;
            color: #667eea;
            margin-bottom: 15px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-bio {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 25px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            display: block;
        }

        .stat-label {
            font-size: 14px;
            color: #999;
            margin-top: 5px;
        }

        .profile-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-profile {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .detail-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 55px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
            font-size: 14px;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .completion-bar {
            background: #f0f0f0;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .completion-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
            border-radius: 10px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 14px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .activity-time {
            font-size: 12px;
            color: #999;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 0 15px;
            }

            .profile-header {
                padding: 30px 20px;
            }

            .profile-stats {
                gap: 20px;
            }

            .profile-actions {
                flex-direction: column;
                align-items: center;
            }

            .profile-details {
                grid-template-columns: 1fr;
            }

            .detail-card {
                padding: 20px;
            }
        }

        .avatar-upload {
            display: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    <div class="profile-page">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    @if ($user->profile_image)
                        <img src="{{ Storage::url($user->profile_image) }}" alt="Profile Picture" id="profileImage">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150&background=667eea&color=ffffff&bold=true"
                            alt="Profile Picture" id="profileImage">
                    @endif
                    <div class="edit-avatar" onclick="document.getElementById('avatarUpload').click()">
                        <i class="fas fa-camera"></i>
                    </div>
                    <input type="file" id="avatarUpload" class="avatar-upload" accept="image/*">
                </div>

                <div class="profile-info">
                    <h1 class="profile-name">{{ $user->name }}</h1>
                    <div class="profile-role">
                        @if ($user->getRoleNames()->isNotEmpty())
                            {{ $user->getRoleNames()->first() }}
                        @else
                            User
                        @endif
                    </div>
                    @if ($user->bio)
                        <p class="profile-bio">{{ $user->bio }}</p>
                    @else
                        <p class="profile-bio text-muted">No bio available. Add one to tell others about yourself!</p>
                    @endif

                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-value" id="profileCompletion">0</span>
                            <div class="stat-label">Profile Complete</div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">{{ $user->login_count ?? 0 }}</span>
                            <div class="stat-label">Total Logins</div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">{{ $user->created_at->format('M Y') }}</span>
                            <div class="stat-label">Member Since</div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="{{ route('admin.profile.edit') }}" class="btn-profile btn-primary">
                            <i class="fas fa-edit"></i>
                            Edit Profile
                        </a>
                        <button class="btn-profile btn-outline" onclick="changePasswordModal()">
                            <i class="fas fa-key"></i>
                            Change Password
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="profile-details">
                <!-- Personal Information -->
                <div class="detail-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="card-title">Personal Information</h3>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value">{{ $user->name }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Email Address</span>
                        <span class="detail-value">{{ $user->email }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value">{{ $user->phone ?? 'Not provided' }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value">
                            {{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Address</span>
                        <span class="detail-value">{{ $user->address ?? 'Not provided' }}</span>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="detail-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3 class="card-title">Account Information</h3>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Account Status</span>
                        <span class="detail-value">
                            <span class="badge bg-success">{{ $user->status->value ?? 'Active' }}</span>
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">User Type</span>
                        <span class="detail-value">{{ $user->usertype->value ?? 'User' }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Member Since</span>
                        <span class="detail-value">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Last Login</span>
                        <span class="detail-value">
                            {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Profile Completion</span>
                        <div class="detail-value">
                            <span id="completionPercentage">0%</span>
                            <div class="completion-bar">
                                <div class="completion-fill" id="completionBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.profile.change-password') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load profile stats
            loadProfileStats();

            // Handle avatar upload
            document.getElementById('avatarUpload').addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    uploadProfileImage(e.target.files[0]);
                }
            });
        });

        function loadProfileStats() {
            fetch('{{ route('admin.profile.stats') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('profileCompletion').textContent = data.profile_completion + '%';
                    document.getElementById('completionPercentage').textContent = data.profile_completion + '%';
                    document.getElementById('completionBar').style.width = data.profile_completion + '%';
                })
                .catch(error => {
                    console.error('Error loading profile stats:', error);
                });
        }

        function uploadProfileImage(file) {
            const formData = new FormData();
            formData.append('profile_image', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Show loading state
            const editIcon = document.querySelector('.edit-avatar i');
            editIcon.className = 'loading-spinner';

            fetch('{{ route('admin.profile.upload-image') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('profileImage').src = data.image_url;
                        showNotification('Profile image updated successfully!', 'success');
                        loadProfileStats(); // Refresh completion percentage
                    } else {
                        showNotification('Failed to upload image: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error uploading image:', error);
                    showNotification('An error occurred while uploading the image.', 'error');
                })
                .finally(() => {
                    // Reset icon
                    editIcon.className = 'fas fa-camera';
                });
        }

        function changePasswordModal() {
            new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className =
                `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    </script>
@endsection
