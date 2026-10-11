import io
from rembg import remove, new_session
from PIL import Image, ImageFilter, ImageDraw, ImageOps

# Load local u2net model session
session = new_session("u2net")

def create_purple_backdrop(size: tuple) -> Image.Image:
    """Creates a dark plum base with a smooth central studio spotlight."""
    width, height = size
    
    # 1. Dark plum base background (#2B133E)
    base_bg = Image.new("RGBA", (width, height), (43, 19, 62, 255))
    
    # 2. Bright purple spotlight layer (#6B2CA3)
    spotlight = Image.new("RGBA", (width, height), (107, 44, 163, 255))
    
    # 3. Create radial mask for the spotlight
    mask = Image.new("L", (width, height), 0)
    draw = ImageDraw.Draw(mask)
    
    center_x, center_y = width // 2, int(height * 0.45)
    radius = int(max(width, height) * 0.5)
    
    # Draw white circle in center and heavily blur for smooth vignette
    draw.ellipse(
        [center_x - radius, center_y - radius, center_x + radius, center_y + radius],
        fill=180
    )
    mask = mask.filter(ImageFilter.GaussianBlur(radius=60))
    
    # Composite spotlight over dark base using the radial mask
    return Image.composite(spotlight, base_bg, mask)


def process_cake_image(image_bytes: bytes) -> bytes:
    input_image = Image.open(io.BytesIO(image_bytes)).convert("RGBA")
    
    # 1. Cut out cake + stand subject
    subject_rgba = remove(input_image, session=session)
    
    # 2. Build purple studio lighting background
    bg = create_purple_backdrop(subject_rgba.size)
    
    # 3. Create realistic floor shadow
    alpha = subject_rgba.split()[3]
    shadow = Image.new("RGBA", subject_rgba.size, (0, 0, 0, 0))
    
    # Soft spread for contact shadow
    shadow_mask = alpha.filter(ImageFilter.GaussianBlur(25))
    shadow.paste((10, 5, 20, 140), mask=shadow_mask)
    
    # Shift shadow 12px down for realistic contact lighting
    shadow_offset = Image.new("RGBA", subject_rgba.size, (0, 0, 0, 0))
    shadow_offset.paste(shadow, (0, 12))
    
    # 4. Composite: Background -> Shadow -> Cake Subject
    composed = Image.alpha_composite(bg, shadow_offset)
    final_rgba = Image.alpha_composite(composed, subject_rgba)
    
    output = io.BytesIO()
    final_rgba.convert("RGB").save(output, format="JPEG", quality=95)
    return output.getvalue()