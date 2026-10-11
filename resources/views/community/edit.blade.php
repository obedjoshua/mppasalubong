<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | Masaganang Pagpapala</title>
    <link href="https://fonts.googleapis.com/css?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body style="background: #F9FAFB;">
    @include('partials.header')
    
    <div style="max-width: 680px; margin: 40px auto; padding: 0 15px;">
        <div style="background: #FFFFFF; border: 2.5px solid #E5E7EB; border-radius: 20px; padding: 35px;">
            <h1 style="font-family: 'modern-bold', sans-serif; font-size: 24px; color: #1F2937; margin-bottom: 20px; border-bottom: 2px solid #F3F4F6; padding-bottom: 15px;">
                Edit Post
            </h1>

            <form action="{{ route('community.post.update', $post->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <textarea name="content" required style="width: 100%; border: 2px solid #E5E7EB; border-radius: 12px; padding: 15px; font-family: 'Manrope', sans-serif; font-size: 15px; resize: vertical; min-height: 150px; margin-bottom: 20px; outline: none; box-sizing: border-box;">{{ $post->content }}</textarea>

                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <a href="{{ route('community.index') }}" style="padding: 12px 20px; border-radius: 12px; font-family: 'modern-bold'; font-size: 16px; color: #4B5563; text-decoration: none; background: #F3F4F6;">Cancel</a>
                    <button type="submit" style="background: #10B981; color: #FFF; border: none; border-radius: 12px; padding: 12px 30px; font-family: 'modern-bold', sans-serif; font-size: 16px; cursor: pointer; box-shadow: 0 4px 0 #059669;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>