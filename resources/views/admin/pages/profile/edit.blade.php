@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .edit-profile-page {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .edit-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .edit-card::before {
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

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .row {
            display: flex;
            margin: 0 -15px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            padding: 0 15px;
        }

        .col-12 {
            flex: 0 0 100%;
            padding: 0 15px;
        }

        .profile-image-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .current-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .image-upload-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
        }

        .image-upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .image-delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
        }

        .btn-save {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-save::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #764ba2, #667eea);
            transition: left 0.3s ease;
            z-index: 1;
        }

        .btn-save:hover::before {
            left: 0;
        }

        .btn-save span {
            position: relative;
            z-index: 2;
        }

        .btn-cancel {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 13px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-cancel:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .file-upload {
            display: none;
        }

        @media (max-width: 768px) {
            .edit-container {
                padding: 0 15px;
            }

            .edit-card {
                padding: 30px 20px;
            }

            .row {
                flex-direction: column;
                margin: 0;
            }

            .col-md-6,
            .col-12 {
                flex: none;
                padding: 0;
            }

            .form-actions {
                flex-direction: column;
                align-items: center;
            }
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    <div class="edit-profile-page">
        <div class="edit-container">
            <div class="edit-card">
                <h1 class="page-title">Edit Profile</h1>

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Image Section -->
                    <div class="profile-image-section">
                        @if ($user->profile_image)
                            <img src="{{ Storage::url($user->profile_image) }}" alt="Current Profile" class="current-image"
                                id="currentImage">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=667eea&color=ffffff&bold=true"
                                alt="Current Profile" class="current-image" id="currentImage">
                        @endif

                        <div>
                            <button type="button" class="image-upload-btn"
                                onclick="document.getElementById('profileImageInput').click()">
                                <i class="fas fa-camera"></i> Change Photo
                            </button>
                            @if ($user->profile_image)
                                <button type="button" class="image-delete-btn" onclick="deleteProfileImage()">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            @endif
                        </div>

                        <input type="file" id="profileImageInput" name="profile_image" class="file-upload"
                            accept="image/*" onchange="previewImage(this)">
                        @error('profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            Personal Information
                        </h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Full Name *</label>
                                    <x-input name="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" :isRequired="true"
                                        :value="old('name', $user->name)" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email Address *</label>
                                    <x-input name="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" :isRequired="true"
                                        :value="old('email', $user->email)" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <x-input name="phone" type="tel"
                                        class="form-control @error('phone') is-invalid @enderror" :value="old('phone', $user->phone)" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Date of Birth</label>
                                    <x-input name="date_of_birth" type="date"
                                        class="form-control @error('date_of_birth') is-invalid @enderror"
                                        :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <x-input name="address" type="textarea"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Enter your full address" attr="rows='3'" :value="old('address', $user->address)" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Bio</label>
                            <x-input name="bio" type="textarea" class="form-control @error('bio') is-invalid @enderror"
                                placeholder="Tell us something about yourself..." attr="rows='4'" :value="old('bio', $user->bio)" />
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <span>Save Changes</span>
                        </button>
                        <a href="{{ route('admin.profile.index') }}" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
@endsection

@section('script')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentImage').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function deleteProfileImage() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                fetch('{{ route('admin.profile.delete-image') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('currentImage').src =
                                'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=667eea&color=ffffff&bold=true';
                            showNotification('Profile image removed successfully!', 'success');

                            // Hide delete button
                            const deleteBtn = document.querySelector('.image-delete-btn');
                            if (deleteBtn) {
                                deleteBtn.style.display = 'none';
                            }
                        } else {
                            showNotification('Failed to remove profile image: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while removing the image.', 'error');
                    });
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className =
                `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Show loading overlay on form submission
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });
    </script>
@endsection
