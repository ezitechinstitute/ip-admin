@php
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $intern->name }} - Intern Portfolio at Ezitech">
    <title>{{ $intern->name }} | Intern Portfolio - Ezitech</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .portfolio-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            margin-bottom: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .profile-id {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .profile-tech {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        /* Content Sections */
        .profile-bio {
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .section-title i {
            color: #667eea;
            font-size: 1.25rem;
        }
        
        /* Skills */
        .skill-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            border-radius: 8px;
            margin: 0.25rem;
            font-size: 0.8rem;
            color: #667eea;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .skill-badge:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        /* Projects Grid */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .project-card {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }
        
        .project-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .project-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .project-date {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        
        .project-description {
            font-size: 0.8rem;
            color: #475569;
            line-height: 1.5;
        }
        
        /* Certificates */
        .certificates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .certificate-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .certificate-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .certificate-card i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .certificate-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .btn-download {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.25rem 0.75rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.7rem;
            transition: all 0.3s ease;
        }
        
        .btn-download:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            color: white;
            opacity: 0.8;
        }
        
        /* Back Link for Authenticated Users */
        .back-link {
            margin-bottom: 1rem;
            text-align: right;
        }
        
        .back-link a {
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .projects-grid {
                grid-template-columns: 1fr;
            }
            .profile-header {
                padding: 1.5rem;
            }
            .profile-avatar {
                width: 80px;
                height: 80px;
            }
            .profile-name {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="portfolio-container">
        @if(Auth::guard('intern')->check() && Auth::guard('intern')->user()->int_id == $intern->int_id)
        <div class="back-link">
            <a href="{{ route('intern.profile') }}">
                <i class="ti ti-arrow-left"></i> Back to My Profile
            </a>
        </div>
        @endif
        
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <img src="{{ $profileImage }}" alt="{{ $intern->name }}" class="profile-avatar">
                <h1 class="profile-name">{{ $intern->name }}</h1>
                <div class="profile-id">{{ $intern->eti_id }}</div>
                <div class="profile-tech">{{ $intern->int_technology ?? 'Technology Intern' }}</div>
            </div>
            
            <!-- Bio -->
            @if($intern->bio)
            <div class="profile-bio">
                <div class="section-title">
                    <i class="ti ti-user"></i>
                    <span>About Me</span>
                </div>
                <p style="line-height: 1.6; color: #475569;">{{ $intern->bio }}</p>
            </div>
            @endif
            
            <!-- Statistics -->
            <div class="profile-bio">
                <div class="section-title">
                    <i class="ti ti-chart-line"></i>
                    <span>Statistics</span>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($stats['total_tasks'] ?? 0) }}</div>
                        <div class="stat-label">Total Tasks</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($stats['completed_tasks'] ?? 0) }}</div>
                        <div class="stat-label">Tasks Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($stats['total_projects'] ?? 0) }}</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($stats['completed_projects'] ?? 0) }}</div>
                        <div class="stat-label">Projects Completed</div>
                    </div>
                </div>
            </div>
            
            <!-- Skills -->
            @if($skills->count() > 0)
            <div class="profile-bio">
                <div class="section-title">
                    <i class="ti ti-code"></i>
                    <span>Technical Skills</span>
                </div>
                <div>
                    @foreach($skills as $skill)
                        <span class="skill-badge">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Projects -->
            @if($projects->count() > 0)
            <div class="profile-bio">
                <div class="section-title">
                    <i class="ti ti-briefcase"></i>
                    <span>Featured Projects</span>
                </div>
                <div class="projects-grid">
                    @foreach($projects as $project)
                    <div class="project-card">
                        <h4 class="project-title">{{ $project->title }}</h4>
                        @if(isset($project->end_date))
                        <div class="project-date">
                            <i class="ti ti-calendar"></i> Completed: {{ Carbon::parse($project->end_date)->format('M Y') }}
                        </div>
                        @endif
                        <p class="project-description">{{ $project->description ?? 'No description available' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Certificates -->
            @if($certificates->count() > 0)
            <div class="profile-bio">
                <div class="section-title">
                    <i class="ti ti-certificate"></i>
                    <span>Certificates</span>
                </div>
                <div class="certificates-grid">
                    @foreach($certificates as $certificate)
                    <div class="certificate-card">
                        <i class="ti ti-certificate"></i>
                        <div class="certificate-title">{{ $certificate->title ?? 'Internship Certificate' }}</div>
                        <div class="project-date">Issued: {{ Carbon::parse($certificate->created_at)->format('M Y') }}</div>
                        @if(isset($certificate->file_path))
                        <a href="{{ asset($certificate->file_path) }}" class="btn-download" download>
                            <i class="ti ti-download"></i> Download
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $intern->name }} • Intern at Ezitech</p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem;">Powered by Ezitech Internship Program</p>
        </div>
    </div>
</body>
</html>