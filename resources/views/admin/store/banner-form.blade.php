@extends('layouts.portal')

@section('title', $banner->exists ? 'Edit hero banner' : 'Create hero banner')

@section('content')
<div class="mb-4">
    <a class="small text-decoration-none" href="{{ route('admin.store.index') }}">← Shop content</a>
    <h1 class="portal-heading h2 mt-2 mb-1">{{ $banner->exists ? 'Edit hero banner' : 'Create hero banner' }}</h1>
    <p class="portal-subtitle mb-0">Design and preview the shop promotion before publishing.</p>
</div>
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
@php($action = $banner->exists ? route('admin.store.banners.update', $banner) : route('admin.store.banners.store'))
<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($banner->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-lg-6">
            <section class="portal-module p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="bannerTitle">Headline</label>
                        <input id="bannerTitle" class="form-control" name="title" maxlength="120" required value="{{ old('title', $banner->title) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="bannerBody">Description</label>
                        <textarea id="bannerBody" class="form-control" name="body" maxlength="240" rows="3">{{ old('body', $banner->body) }}</textarea>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-semibold" for="bannerLink">Link URL</label>
                        <input id="bannerLink" class="form-control" name="link_url" maxlength="2048" value="{{ old('link_url', $banner->link_url) }}" placeholder="/shop">
                        <div class="form-text">Use a site path or HTTPS URL.</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold" for="bannerCta">Button label</label>
                        <input id="bannerCta" class="form-control" name="cta_label" maxlength="40" value="{{ old('cta_label', $banner->cta_label) }}" placeholder="Explore the shop">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="bannerImage">Banner image</label>
                        <input id="bannerImage" class="form-control" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(!$banner->exists)>
                        <div class="form-text">JPG, PNG, or WebP; up to 5 MB.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="bannerBackground">Background color</label>
                        <input id="bannerBackground" class="form-control form-control-color w-100" name="background_color" type="color" value="{{ old('background_color', $banner->background_color ?: '#310669') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="bannerTextColor">Text color</label>
                        <input id="bannerTextColor" class="form-control form-control-color w-100" name="text_color" type="color" value="{{ old('text_color', $banner->text_color ?: '#FFFFFF') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="bannerAlignment">Text alignment</label>
                        <select id="bannerAlignment" class="form-select" name="alignment">
                            @foreach(['left', 'center', 'right'] as $alignment)
                                <option value="{{ $alignment }}" @selected(old('alignment', $banner->alignment ?: 'left') === $alignment)>{{ ucfirst($alignment) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="bannerLayout">Image layout</label>
                        <select id="bannerLayout" class="form-select" name="layout">
                            <option value="split" @selected(old('layout', $banner->layout ?: 'split') === 'split')>Split / shaded</option>
                            <option value="overlay" @selected(old('layout', $banner->layout ?: 'split') === 'overlay')>Full overlay</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="bannerOrder">Display order</label>
                        <input id="bannerOrder" class="form-control" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="bannerVisibility">Visibility</label>
                        <select id="bannerVisibility" class="form-select" name="is_active">
                            <option value="1" @selected((string) old('is_active', $banner->is_active ?? 1) === '1')>Published</option>
                            <option value="0" @selected((string) old('is_active', $banner->is_active ?? 1) === '0')>Hidden</option>
                        </select>
                    </div>
                </div>
                <p class="portal-muted small mt-3">Text is displayed as plain text; custom HTML, CSS, and scripts are not accepted.</p>
                <button class="btn btn-primary" type="submit">{{ $banner->exists ? 'Save banner' : 'Publish banner' }}</button>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="portal-module">
                <div class="portal-module-header">
                    <div>
                        <h2 class="portal-module-title">Live preview</h2>
                        <p class="portal-muted small mb-0">Preview updates as you edit.</p>
                    </div>
                    <span class="portal-pill">Shop hero</span>
                </div>
                @php($bannerPreviewImage = $banner->image_path ? asset('storage/'.$banner->image_path) : '')
                <section id="bannerPreview" class="portal-banner-preview" data-layout="{{ old('layout', $banner->layout ?: 'split') }}" data-alignment="{{ old('alignment', $banner->alignment ?: 'left') }}" style="background-color:{{ old('background_color', $banner->background_color ?: '#310669') }};color:{{ old('text_color', $banner->text_color ?: '#FFFFFF') }}">
                    <img id="bannerPreviewImage" src="{{ $bannerPreviewImage }}" alt="" @if(!$bannerPreviewImage) hidden @endif>
                    <div class="portal-banner-copy">
                        <h2 id="bannerTitlePreview">{{ old('title', $banner->title ?: 'A little something special') }}</h2>
                        <p id="bannerBodyPreview">{{ old('body', $banner->body ?: 'Your banner description will appear here.') }}</p>
                        <span id="bannerCtaPreview">{{ old('cta_label', $banner->cta_label ?: 'Explore the shop') }}</span>
                    </div>
                </section>
                <p class="portal-muted small p-3 mb-0">The storefront uses this same bounded layout and color palette.</p>
            </section>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const bannerField = name => document.querySelector(`[name="${name}"]`);
    const bannerPreview = document.getElementById('bannerPreview');
    bannerField('title').addEventListener('input', event => document.getElementById('bannerTitlePreview').textContent = event.target.value || 'A little something special');
    bannerField('body').addEventListener('input', event => document.getElementById('bannerBodyPreview').textContent = event.target.value || 'Your banner description will appear here.');
    bannerField('cta_label').addEventListener('input', event => document.getElementById('bannerCtaPreview').textContent = event.target.value || 'Explore the shop');
    bannerField('background_color').addEventListener('input', event => bannerPreview.style.backgroundColor = event.target.value);
    bannerField('text_color').addEventListener('input', event => bannerPreview.style.color = event.target.value);
    bannerField('alignment').addEventListener('change', event => bannerPreview.dataset.alignment = event.target.value);
    bannerField('layout').addEventListener('change', event => bannerPreview.dataset.layout = event.target.value);
    bannerField('image').addEventListener('change', event => {
        if (event.target.files[0]) {
            const image = document.getElementById('bannerPreviewImage');
            image.hidden = false;
            image.src = URL.createObjectURL(event.target.files[0]);
        }
    });
</script>
@endpush
