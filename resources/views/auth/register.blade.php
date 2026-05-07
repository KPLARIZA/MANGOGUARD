<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MangoGuard - Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background:
                linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                url('https://static.vecteezy.com/system/resources/thumbnails/029/311/321/small_2x/mango-pattern-background-ai-generated-free-photo.jpg')
                center/cover no-repeat fixed;

            min-height: 100vh;
        }

        .register-container {
            background: rgba(255,255,255,0.92);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        .mango-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-6 col-lg-4">

            <div class="register-container">

                <div class="text-center">
                    <div class="mango-icon">🥭</div>
                    <h2 class="mb-4">Create Account</h2>
                </div>

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">

                        {{ session('success') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif

                {{-- Error Message --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">

                        {{ session('error') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">

                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">

                    @csrf

                    <div class="mb-3">
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Full Name"
                            value="{{ old('name') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Email"
                            value="{{ old('email') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Password"
                            required>
                    </div>

                    <div class="mb-3">
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Confirm Password"
                            required>
                    </div>

                    <div class="mb-3">
                        <select name="gender"
                                class="form-control">

                            <option value="male">Male</option>
                            <option value="female">Female</option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            placeholder="Phone (optional)"
                            value="{{ old('phone') }}">
                    </div>

                    <button type="submit"
                            class="btn btn-warning w-100">
                        Register
                    </button>

                </form>

                <p class="text-center mt-3 mb-0">
                    Already have an account?

                    <a href="{{ route('login') }}"
                       class="text-warning text-decoration-none">
                        Login
                    </a>
                </p>

            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>