<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <title>Start Your Order - Masaganang Pagpapala</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-100 flex items-center justify-center min-h-screen p-4 font-sans text-zinc-900">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <div class="bg-zinc-900 text-white md:w-1/2 p-10 flex flex-col justify-center">
            <h1 class="text-4xl font-extrabold mb-4">Ready to Order?</h1>
            <p class="text-zinc-400 mb-8">Choose how you'd like to proceed with your custom cake request.</p>
            
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="bg-zinc-800 p-2 rounded-full mr-4">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold">Track Your Order Status</h3>
                        <p class="text-sm text-zinc-400">Log in to view live updates from pending review to ready for pick-up.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-zinc-800 p-2 rounded-full mr-4">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold">Unlock Avatar Frames</h3>
                        <p class="text-sm text-zinc-400">Earn badges and community frames when you order using an account.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="md:w-1/2 p-10 flex flex-col justify-center space-y-6 bg-white">
            
            <!-- Login Option (Changed from div with onclick to an 'a' tag) -->
            <a href="{{ route('login') }}" class="block border-2 border-zinc-200 rounded-xl p-6 hover:border-zinc-900 transition cursor-pointer text-left focus:outline-none focus:ring-2 focus:ring-zinc-900">
                <h2 class="text-xl font-bold mb-2">Sign in to Order</h2>
                <p class="text-sm text-zinc-500 mb-4">Access your saved addresses, track your ticket, and earn community rewards.</p>
                <span class="inline-block bg-zinc-900 text-white font-bold py-2 px-6 rounded hover:bg-zinc-800 transition">Log In / Register</span>
            </a>

            <div class="flex items-center text-zinc-400 text-sm">
                <div class="flex-grow border-t border-zinc-200"></div>
                <span class="px-3 uppercase tracking-widest font-bold">OR</span>
                <div class="flex-grow border-t border-zinc-200"></div>
            </div>

            <!-- Guest Checkout Option (Changed from div with onclick to an 'a' tag) -->
            <a href="{{ route('orders.create') }}" class="block border-2 border-zinc-100 rounded-xl p-6 bg-zinc-50 hover:border-zinc-300 transition cursor-pointer text-left focus:outline-none focus:ring-2 focus:ring-zinc-300">
                <h2 class="text-lg font-bold mb-2">Continue as Guest</h2>
                <p class="text-sm text-zinc-500 mb-4">Submit a one-time order form. You will need to check your email/SMS manually for the payment link.</p>
                <span class="inline-block border border-zinc-300 bg-white text-zinc-900 font-bold py-2 px-6 rounded hover:bg-zinc-100 transition">Proceed to Order Form</span>
            </a>

        </div>
    </div>
    
</body>
</html>