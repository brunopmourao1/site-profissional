<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/site_data.php';

$data = load_site_data();
$meta = $data['meta'];
$sections = $data['sections'];
$brandFont = resolve_font_family((string)($meta['brand_font'] ?? 'sora'));
$brandSize = normalize_text_size($meta['brand_size'] ?? 18);
$brandDisplay = normalize_brand_display($meta['brand_display'] ?? 'both');
$brandHasLogo = !empty($meta['logo_image']);
$brandShowLogo = ($brandDisplay === 'logo' || $brandDisplay === 'both') && $brandHasLogo;
$brandShowText = ($brandDisplay === 'text' || $brandDisplay === 'both') || (!$brandHasLogo && $brandDisplay === 'logo');
$headlineFont = resolve_font_family((string)($meta['headline_font'] ?? 'sora'));
$headlineSize = normalize_text_size($meta['headline_size'] ?? 42);
$heroTextColor = (string)($meta['hero_text_color'] ?? '#f6f2eb');
$introFont = resolve_font_family((string)($meta['intro_font'] ?? 'manrope'));
$introSize = normalize_text_size($meta['intro_size'] ?? 17);
$footerFont = resolve_font_family((string)($meta['footer_font'] ?? 'manrope'));
$footerSize = normalize_text_size($meta['footer_size'] ?? 16);
$animationStyle = (string)($meta['animation_style'] ?? 'slide');
$heroMotion = (string)($meta['hero_motion'] ?? 'soft');
$heroOverline = trim((string)($meta['hero_overline'] ?? 'Apresentacao profissional'));
$heroMode = (string)($meta['hero_mode'] ?? 'text');
$heroVideo = (string)($meta['hero_video'] ?? '');
$heroImage = (string)($meta['hero_image'] ?? '');
$heroLayout = (string)($meta['hero_layout'] ?? 'split-right');
$heroSplitSize = (string)($meta['hero_split_size'] ?? 'medium');
$heroSplitFit = (string)($meta['hero_split_fit'] ?? 'cover');
$heroSplitWidth = normalize_dimension_size($meta['hero_split_width'] ?? 0);
$heroSplitHeight = normalize_dimension_size($meta['hero_split_height'] ?? 0);
$heroModeEffective = $heroMode === 'video' ? 'video_full' : $heroMode;
if (!in_array($heroModeEffective, ['text', 'video_full', 'video_background', 'video_split', 'image_full', 'image_background', 'image_split'], true)) {
    $heroModeEffective = 'text';
}
$heroLayoutEffective = in_array($heroLayout, ['split-left', 'split-right'], true) ? $heroLayout : 'split-right';
$heroSplitSizeEffective = in_array($heroSplitSize, ['small', 'medium', 'large'], true) ? $heroSplitSize : 'medium';
$heroSplitFitEffective = in_array($heroSplitFit, ['cover', 'contain'], true) ? $heroSplitFit : 'cover';
$heroSplitStyle = '';
if ($heroSplitWidth > 0) {
    $heroSplitStyle .= '--hero-split-width:' . $heroSplitWidth . 'px;';
}
if ($heroSplitHeight > 0) {
    $heroSplitStyle .= '--hero-split-height:' . $heroSplitHeight . 'px;';
}
$heroSplitHasCustomWidth = $heroSplitWidth > 0;
$heroSplitHasCustomHeight = $heroSplitHeight > 0;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($meta['site_name'] ?? 'Site Institucional') ?></title>
    <link rel="stylesheet" href="assets/site.css">
</head>
<body class="anim-<?= e($animationStyle) ?> hero-<?= e($heroMotion) ?>">
<header class="topbar">
    <a class="brand brand-mode-<?= e($brandDisplay) ?>" href="index.php#inicio" aria-label="Ir para o inicio do site">
        <?php if ($brandShowLogo): ?>
            <img src="<?= e($meta['logo_image']) ?>" alt="<?= e($meta['logo_alt'] ?? 'Logo da empresa') ?>" class="brand-logo">
        <?php endif; ?>
        <?php if ($brandShowText): ?>
            <span class="brand-text" style="font-family: <?= e($brandFont) ?>; font-size: <?= $brandSize ?>px;"><?= e($meta['site_name'] ?? 'Minha Empresa') ?></span>
        <?php endif; ?>
    </a>
    <button class="menu-toggle" aria-label="Abrir menu">Menu</button>
    <nav class="menu" id="menu">
        <a href="#inicio">Inicio</a>
        <?php foreach ($sections as $section): ?>
            <a href="#<?= e($section['slug'] ?? '') ?>"><?= e($section['menu_label'] ?? ($section['title'] ?? 'Secao')) ?></a>
        <?php endforeach; ?>
    </nav>
</header>

<section id="inicio" class="hero<?= ($heroModeEffective === 'video_full' && $heroVideo !== '') ? ' hero-video-mode' : '' ?><?= ($heroModeEffective === 'video_background' && $heroVideo !== '') ? ' hero-video-background' : '' ?><?= ($heroModeEffective === 'image_full' && $heroImage !== '') ? ' hero-image-mode' : '' ?><?= ($heroModeEffective === 'image_background' && $heroImage !== '') ? ' hero-image-background' : '' ?>" style="--primary: <?= e($meta['primary_color'] ?? '#113946') ?>; --secondary: <?= e($meta['secondary_color'] ?? '#bca37f') ?>;">
    <?php if ($heroModeEffective === 'video_full' && $heroVideo !== ''): ?>
        <div class="hero-video-wrap reveal anim-<?= e($animationStyle) ?>">
            <video class="hero-video" src="<?= e($heroVideo) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
        </div>
    <?php elseif ($heroModeEffective === 'video_background' && $heroVideo !== ''): ?>
        <video class="hero-bg-video" src="<?= e($heroVideo) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
        <div class="hero-content reveal anim-<?= e($animationStyle) ?>" style="color: <?= e($heroTextColor) ?>;">
            <?php if ($heroOverline !== ''): ?><p class="overline"><?= e($heroOverline) ?></p><?php endif; ?>
            <h1 style="font-family: <?= e($headlineFont) ?>; font-size: clamp(2rem, 6vw, <?= $headlineSize ?>px);"><?= e($meta['headline'] ?? '') ?></h1>
            <p style="font-family: <?= e($introFont) ?>; font-size: <?= $introSize ?>px;"><?= nl2br(e($meta['intro'] ?? '')) ?></p>
            <div class="hero-cta">
                <?php if (!empty($meta['whatsapp'])): ?>
                    <a class="btn" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string)$meta['whatsapp'])) ?>">Falar no WhatsApp</a>
                <?php endif; ?>
                <a class="btn ghost" href="#contatos">Ver contatos</a>
            </div>
        </div>
    <?php elseif ($heroModeEffective === 'video_split' && $heroVideo !== ''): ?>
        <div class="split-wrap hero-split-wrap split-size-<?= e($heroSplitSizeEffective) ?> split-fit-<?= e($heroSplitFitEffective) ?> hero-split-<?= e($heroLayoutEffective) ?><?= $heroSplitHasCustomWidth ? ' has-custom-width' : '' ?><?= $heroSplitHasCustomHeight ? ' has-custom-height' : '' ?> reveal anim-<?= e($animationStyle) ?>"<?= $heroSplitStyle !== '' ? ' style="' . e($heroSplitStyle) . '"' : '' ?>>
            <div class="split-media hero-split-media">
                <video class="split-video" src="<?= e($heroVideo) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
            </div>
            <div class="split-text hero-split-text" style="color: <?= e($heroTextColor) ?>;">
                <?php if ($heroOverline !== ''): ?><p class="overline"><?= e($heroOverline) ?></p><?php endif; ?>
                <h1 style="font-family: <?= e($headlineFont) ?>; font-size: clamp(2rem, 6vw, <?= $headlineSize ?>px);"><?= e($meta['headline'] ?? '') ?></h1>
                <p style="font-family: <?= e($introFont) ?>; font-size: <?= $introSize ?>px;"><?= nl2br(e($meta['intro'] ?? '')) ?></p>
                <div class="hero-cta">
                    <?php if (!empty($meta['whatsapp'])): ?>
                        <a class="btn" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string)$meta['whatsapp'])) ?>">Falar no WhatsApp</a>
                    <?php endif; ?>
                    <a class="btn ghost" href="#contatos">Ver contatos</a>
                </div>
            </div>
        </div>
    <?php elseif ($heroModeEffective === 'image_full' && $heroImage !== ''): ?>
        <div class="hero-image-wrap reveal anim-<?= e($animationStyle) ?>">
            <img class="hero-image" src="<?= e($heroImage) ?>" alt="<?= e($meta['headline'] ?? 'Imagem principal') ?>">
        </div>
    <?php elseif ($heroModeEffective === 'image_background' && $heroImage !== ''): ?>
        <img class="hero-bg-image" src="<?= e($heroImage) ?>" alt="<?= e($meta['headline'] ?? 'Imagem principal') ?>">
        <div class="hero-content reveal anim-<?= e($animationStyle) ?>" style="color: <?= e($heroTextColor) ?>;">
            <?php if ($heroOverline !== ''): ?><p class="overline"><?= e($heroOverline) ?></p><?php endif; ?>
            <h1 style="font-family: <?= e($headlineFont) ?>; font-size: clamp(2rem, 6vw, <?= $headlineSize ?>px);"><?= e($meta['headline'] ?? '') ?></h1>
            <p style="font-family: <?= e($introFont) ?>; font-size: <?= $introSize ?>px;"><?= nl2br(e($meta['intro'] ?? '')) ?></p>
            <div class="hero-cta">
                <?php if (!empty($meta['whatsapp'])): ?>
                    <a class="btn" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string)$meta['whatsapp'])) ?>">Falar no WhatsApp</a>
                <?php endif; ?>
                <a class="btn ghost" href="#contatos">Ver contatos</a>
            </div>
        </div>
    <?php elseif ($heroModeEffective === 'image_split' && $heroImage !== ''): ?>
        <div class="split-wrap hero-split-wrap split-size-<?= e($heroSplitSizeEffective) ?> split-fit-<?= e($heroSplitFitEffective) ?> hero-split-<?= e($heroLayoutEffective) ?><?= $heroSplitHasCustomWidth ? ' has-custom-width' : '' ?><?= $heroSplitHasCustomHeight ? ' has-custom-height' : '' ?> reveal anim-<?= e($animationStyle) ?>"<?= $heroSplitStyle !== '' ? ' style="' . e($heroSplitStyle) . '"' : '' ?>>
            <div class="split-media hero-split-media">
                <img src="<?= e($heroImage) ?>" alt="<?= e($meta['headline'] ?? 'Imagem principal') ?>">
            </div>
            <div class="split-text hero-split-text" style="color: <?= e($heroTextColor) ?>;">
                <?php if ($heroOverline !== ''): ?><p class="overline"><?= e($heroOverline) ?></p><?php endif; ?>
                <h1 style="font-family: <?= e($headlineFont) ?>; font-size: clamp(2rem, 6vw, <?= $headlineSize ?>px);"><?= e($meta['headline'] ?? '') ?></h1>
                <p style="font-family: <?= e($introFont) ?>; font-size: <?= $introSize ?>px;"><?= nl2br(e($meta['intro'] ?? '')) ?></p>
                <div class="hero-cta">
                    <?php if (!empty($meta['whatsapp'])): ?>
                        <a class="btn" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string)$meta['whatsapp'])) ?>">Falar no WhatsApp</a>
                    <?php endif; ?>
                    <a class="btn ghost" href="#contatos">Ver contatos</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-content reveal anim-<?= e($animationStyle) ?>" style="color: <?= e($heroTextColor) ?>;">
            <?php if ($heroOverline !== ''): ?><p class="overline"><?= e($heroOverline) ?></p><?php endif; ?>
            <h1 style="font-family: <?= e($headlineFont) ?>; font-size: clamp(2rem, 6vw, <?= $headlineSize ?>px);"><?= e($meta['headline'] ?? '') ?></h1>
            <p style="font-family: <?= e($introFont) ?>; font-size: <?= $introSize ?>px;"><?= nl2br(e($meta['intro'] ?? '')) ?></p>
            <div class="hero-cta">
                <?php if (!empty($meta['whatsapp'])): ?>
                    <a class="btn" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string)$meta['whatsapp'])) ?>">Falar no WhatsApp</a>
                <?php endif; ?>
                <a class="btn ghost" href="#contatos">Ver contatos</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php foreach ($sections as $section):
    $layoutMode = (string)($section['layout_mode'] ?? 'background');
    $splitSize = (string)($section['split_size'] ?? 'medium');
    $splitFit = (string)($section['split_fit'] ?? 'cover');
    $splitWidth = normalize_dimension_size($section['split_width'] ?? 0);
    $splitHeight = normalize_dimension_size($section['split_height'] ?? 0);
    $splitHasCustomWidth = $splitWidth > 0;
    $splitHasCustomHeight = $splitHeight > 0;
    $hasImage = !empty($section['background_image']);
    $hasSplitImage = !empty($section['split_image']);
    $sectionMode = (string)($section['section_mode'] ?? 'text');
    $sectionVideo = (string)($section['section_video'] ?? '');
    $isVideoFull = in_array($sectionMode, ['video', 'video_full'], true) && $sectionVideo !== '';
    $isVideoBackground = $sectionMode === 'video_background' && $sectionVideo !== '';
    $isVideoSplit = $sectionMode === 'video_split' && $sectionVideo !== '';
    $effectiveSplitLayout = ($layoutMode === 'split-left' || $layoutMode === 'split-right') ? $layoutMode : 'split-right';
    $sectionAnimation = (string)($section['section_animation'] ?? 'inherit');
    $effectiveSectionAnim = $sectionAnimation === 'inherit' ? $animationStyle : $sectionAnimation;
    $titleFontFamily = resolve_font_family((string)($section['title_font'] ?? 'sora'));
    $titleSize = normalize_text_size($section['title_size'] ?? 40);
    $fontFamily = resolve_font_family((string)($section['text_font'] ?? 'manrope'));
    $fontSize = normalize_text_size($section['text_size'] ?? 18);
    $textColor = (string)($section['text_color'] ?? '#102133');
    $frameBgMode = normalize_frame_bg_mode($section['frame_bg_mode'] ?? 'default');
    $frameBgColor = normalize_hex_color($section['frame_bg_color'] ?? '#fffdfa', '#fffdfa');
    $frameBgValue = '';
    if ($frameBgMode === 'section') {
        $frameBgValue = normalize_hex_color($section['background_color'] ?? '#ffffff', '#ffffff');
    } elseif ($frameBgMode === 'custom') {
        $frameBgValue = $frameBgColor;
    }
    $frameVars = $frameBgValue !== ''
        ? '--frame-bg:' . $frameBgValue . ';--subframe-bg:' . $frameBgValue . ';'
        : '';
    $youtubeEmbedUrl = youtube_embed_url((string)($section['youtube_url'] ?? ''));
    $mapEmbedUrl = trim((string)($section['map_embed_url'] ?? ''));
    $sectionFeature = (string)($section['section_feature'] ?? '');
    if (!in_array($sectionFeature, ['none', 'youtube', 'map', 'contact_form', 'linked_gallery', 'cards', 'carousel'], true)) {
        if (!empty($section['linked_images']) && is_array($section['linked_images'])) {
            $sectionFeature = 'linked_gallery';
        } elseif (!empty($section['carousel_items']) && is_array($section['carousel_items']) && count($section['carousel_items']) >= 2) {
            $sectionFeature = 'carousel';
        } elseif (!empty($section['cards_items']) && is_array($section['cards_items'])) {
            $sectionFeature = 'cards';
        } elseif (!empty($section['contact_enabled'])) {
            $sectionFeature = 'contact_form';
        } elseif (!empty($section['map_enabled'])) {
            $sectionFeature = 'map';
        } elseif (!empty($section['youtube_enabled'])) {
            $sectionFeature = 'youtube';
        } else {
            $sectionFeature = 'none';
        }
    }
    $contactFormTitle = trim((string)($section['contact_form_title'] ?? 'Envie sua mensagem'));
    if ($contactFormTitle === '') {
        $contactFormTitle = 'Envie sua mensagem';
    }
    $contactDestination = trim((string)($section['contact_destination_email'] ?? ''));
    if ($contactDestination === '') {
        $contactDestination = trim((string)($meta['contact_email'] ?? ''));
    }
    $contactDestination = filter_var($contactDestination, FILTER_VALIDATE_EMAIL) ? $contactDestination : '';
    $contactButtonText = trim((string)($section['contact_button_text'] ?? 'Enviar'));
    if ($contactButtonText === '') {
        $contactButtonText = 'Enviar';
    }
    $showYouTube = $sectionFeature === 'youtube' && $youtubeEmbedUrl !== '';
    $showMap = $sectionFeature === 'map' && $mapEmbedUrl !== '';
    $showContactForm = $sectionFeature === 'contact_form' && $contactDestination !== '';
    $linkedImagesLayout = normalize_linked_images_layout($section['linked_images_layout'] ?? 'mosaic');
    $linkedImagesLimit = normalize_linked_images_limit_for_layout($section['linked_images_limit'] ?? 6, $linkedImagesLayout);
    $linkedImagesRaw = $section['linked_images'] ?? [];
    $linkedImages = [];
    if (is_array($linkedImagesRaw)) {
        foreach ($linkedImagesRaw as $linkedImage) {
            if (!is_array($linkedImage)) {
                continue;
            }
            $linkedImagePath = trim((string)($linkedImage['image'] ?? ''));
            if ($linkedImagePath === '') {
                continue;
            }
            $linkedImageLink = trim((string)($linkedImage['link'] ?? ''));
            $linkedImageAlt = trim((string)($linkedImage['alt'] ?? ''));
            if ($linkedImageAlt === '') {
                $linkedImageAlt = 'Imagem relacionada a ' . ((string)($section['title'] ?? 'Secao'));
            }
            $linkedImages[] = [
                'image' => $linkedImagePath,
                'link' => $linkedImageLink,
                'alt' => $linkedImageAlt,
            ];
            if (count($linkedImages) >= $linkedImagesLimit) {
                break;
            }
        }
    }
    $showLinkedGallery = $sectionFeature === 'linked_gallery' && count($linkedImages) >= 2;
    $showMosaicLinkedGallery = $showLinkedGallery && $linkedImagesLayout === 'mosaic';
    $showLogosLinkedGallery = $showLinkedGallery && $linkedImagesLayout === 'logos';

    $carouselLimit = normalize_carousel_limit($section['carousel_limit'] ?? 2);
    $carouselLayout = normalize_carousel_layout($section['carousel_layout'] ?? 'boxed');
    $carouselRaw = $section['carousel_items'] ?? [];
    $carouselItemsById = [];
    if (is_array($carouselRaw)) {
        foreach ($carouselRaw as $carouselItem) {
            if (!is_array($carouselItem)) {
                continue;
            }
            $carouselId = (int)($carouselItem['id'] ?? 0);
            if ($carouselId < 1 || $carouselId > 5) {
                continue;
            }
            if ($carouselId > $carouselLimit) {
                continue;
            }
            $carouselSrc = trim((string)($carouselItem['src'] ?? ''));
            if ($carouselSrc === '') {
                continue;
            }
            $carouselType = trim((string)($carouselItem['type'] ?? ''));
            if (!in_array($carouselType, ['image', 'video'], true)) {
                $ext = strtolower(pathinfo($carouselSrc, PATHINFO_EXTENSION));
                $carouselType = in_array($ext, ['mp4', 'webm', 'ogg'], true) ? 'video' : 'image';
            }
            $carouselLink = trim((string)($carouselItem['link'] ?? ''));
            $carouselAlt = trim((string)($carouselItem['alt'] ?? ''));
            if ($carouselAlt === '') {
                $carouselAlt = 'Midia do carousel de ' . ((string)($section['title'] ?? 'Secao'));
            }
            $carouselItemsById[$carouselId] = [
                'type' => $carouselType,
                'src' => $carouselSrc,
                'link' => $carouselLink,
                'alt' => $carouselAlt,
            ];
        }
    }
    ksort($carouselItemsById);
    $carouselItems = array_values($carouselItemsById);
    $showCarousel = $sectionFeature === 'carousel' && count($carouselItems) >= 2;

    $cardsTitle = trim((string)($section['cards_title'] ?? ''));
    if ($cardsTitle === '') {
        $cardsTitle = trim((string)($section['title'] ?? ''));
    }
    if ($cardsTitle === '') {
        $cardsTitle = 'Cards';
    }
    $cardsItemsRaw = $section['cards_items'] ?? [];
    $cardsLimit = normalize_cards_limit($section['cards_limit'] ?? 6);
    $cardsSpacing = normalize_cards_spacing($section['cards_spacing'] ?? 'spaced');
    $cardsStyle = trim((string)($section['cards_style'] ?? 'media'));
    if (!in_array($cardsStyle, ['media', 'text'], true)) {
        $cardsStyle = 'media';
    }
    $cardsItems = [];
    if (is_array($cardsItemsRaw)) {
        foreach ($cardsItemsRaw as $cardItem) {
            if (!is_array($cardItem)) {
                continue;
            }
            $cardTitle = trim((string)($cardItem['title'] ?? ''));
            $cardText = trim((string)($cardItem['text'] ?? ''));
            $cardImage = trim((string)($cardItem['image'] ?? ''));
            if ($cardsStyle === 'text') {
                $cardImage = '';
            }
            $cardButtonText = trim((string)($cardItem['button_text'] ?? ''));
            $cardButtonLink = trim((string)($cardItem['button_link'] ?? ''));
            if ($cardTitle === '' && $cardText === '' && $cardImage === '') {
                continue;
            }
            if ($cardButtonLink !== '' && $cardButtonText === '') {
                $cardButtonText = 'Saiba mais';
            }
            $cardsItems[] = [
                'title' => $cardTitle,
                'text' => $cardText,
                'image' => $cardImage,
                'button_text' => $cardButtonText,
                'button_link' => $cardButtonLink,
            ];
            if (count($cardsItems) >= $cardsLimit) {
                break;
            }
        }
    }
    $showCards = $sectionFeature === 'cards' && count($cardsItems) > 0;
    $contentRaw = (string)($section['content'] ?? '');
    $style = $hasImage
        ? "background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)), url('" . e($section['background_image']) . "');"
        : "background-color: " . e($section['background_color'] ?? '#ffffff') . ";";
    $mosaicStyle = $hasImage
        ? "background-image: url('" . e($section['background_image']) . "'); background-size: cover; background-position: center;"
        : "background-color: " . e($section['background_color'] ?? '#ffffff') . ";";
    $directStyle = $hasImage
        ? "background-image: linear-gradient(rgba(7,10,14,.68), rgba(7,10,14,.68)), url('" . e($section['background_image']) . "'); background-size: cover; background-position: center;"
        : "background: linear-gradient(120deg, #10151d, #151b23 54%, #0c1118);";
    $splitStyleVars = '';
    if ($splitWidth > 0) {
        $splitStyleVars .= '--split-media-width:' . $splitWidth . 'px;';
    }
    if ($splitHeight > 0) {
        $splitStyleVars .= '--split-media-height:' . $splitHeight . 'px;';
    }
    $splitSectionStyle = 'background-color: ' . e($section['background_color'] ?? '#ffffff') . ';' . $splitStyleVars;
?>
<?php if ($showMosaicLinkedGallery): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-gallery-mosaic mosaic-count-<?= count($linkedImages) ?>" style="<?= $mosaicStyle ?><?= $frameVars ?>">
    <div class="section-gallery-mosaic-grid reveal anim-<?= e($effectiveSectionAnim) ?>">
        <?php foreach ($linkedImages as $linkedImage): ?>
            <?php if ($linkedImage['link'] !== ''): ?>
                <a class="mosaic-item" href="<?= e($linkedImage['link']) ?>" target="_blank" rel="noopener">
                    <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                    <span class="mosaic-overlay" aria-hidden="true"><span class="mosaic-btn">Acessar</span></span>
                </a>
            <?php else: ?>
                <div class="mosaic-item is-static">
                    <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php elseif ($showLogosLinkedGallery): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-gallery-direct" style="<?= $directStyle ?><?= $frameVars ?>color: <?= e($textColor) ?>;">
    <div class="section-gallery-direct-inner reveal anim-<?= e($effectiveSectionAnim) ?>">
        <h2 class="section-gallery-direct-title" style="font-family: <?= e($titleFontFamily) ?>; font-size: clamp(1.6rem, 3.3vw, <?= $titleSize ?>px);"><?= e($section['title'] ?? 'Secao') ?></h2>
        <?php if (trim(strip_tags($contentRaw)) !== ''): ?>
            <div class="section-gallery-direct-desc" style="font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
                <?php if (strip_tags($contentRaw) === $contentRaw): ?>
                    <p><?= nl2br(e($contentRaw)) ?></p>
                <?php else: ?>
                    <?= sanitize_rich_text($contentRaw) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="section-linked-gallery section-linked-gallery-direct">
            <?php foreach ($linkedImages as $linkedImage): ?>
                <?php if ($linkedImage['link'] !== ''): ?>
                    <a class="linked-gallery-item" href="<?= e($linkedImage['link']) ?>" target="_blank" rel="noopener">
                        <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                    </a>
                <?php else: ?>
                    <div class="linked-gallery-item">
                        <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php elseif ($showCards): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-cards-block" style="background-color: <?= e($section['background_color'] ?? '#f5f6f8') ?>;<?= $frameVars ?>color: <?= e($textColor) ?>;">
    <div class="section-cards-wrap reveal anim-<?= e($effectiveSectionAnim) ?>" style="font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
        <h2 class="section-cards-title" style="font-family: <?= e($titleFontFamily) ?>; font-size: clamp(1.6rem, 3.3vw, <?= $titleSize ?>px);"><?= e($cardsTitle) ?></h2>
        <div class="section-cards-grid cards-spacing-<?= e($cardsSpacing) ?>">
            <?php foreach ($cardsItems as $cardItem): ?>
                <article class="section-feature-card">
                    <?php if ($cardItem['image'] !== ''): ?>
                        <div class="section-feature-card-media">
                            <img src="<?= e($cardItem['image']) ?>" alt="<?= e($cardItem['title'] !== '' ? $cardItem['title'] : 'Imagem do card') ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <div class="section-feature-card-body">
                        <?php if ($cardItem['title'] !== ''): ?>
                            <h3 style="font-family: <?= e($titleFontFamily) ?>;"><?= e($cardItem['title']) ?></h3>
                        <?php endif; ?>
                        <?php if ($cardItem['text'] !== ''): ?>
                            <p><?= nl2br(e($cardItem['text'])) ?></p>
                        <?php endif; ?>
                        <?php if ($cardItem['button_link'] !== '' && $cardItem['button_text'] !== ''): ?>
                            <a class="section-feature-card-btn" href="<?= e($cardItem['button_link']) ?>" target="_blank" rel="noopener"><?= e($cardItem['button_text']) ?></a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php elseif ($showCarousel && $carouselLayout === 'full'): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-carousel-full" style="background-color: <?= e($section['background_color'] ?? '#ffffff') ?>;<?= $frameVars ?>">
    <div class="carousel carousel-full reveal anim-<?= e($effectiveSectionAnim) ?>" data-carousel="true">
        <div class="carousel-viewport">
            <button type="button" class="carousel-nav carousel-prev" aria-label="Slide anterior">
                <span aria-hidden="true">&lsaquo;</span>
            </button>
            <div class="carousel-track">
                <?php foreach ($carouselItems as $carouselIndex => $carouselItem): ?>
                    <?php
                    $carouselType = (string)($carouselItem['type'] ?? 'image');
                    $carouselSrc = (string)($carouselItem['src'] ?? '');
                    $carouselLink = (string)($carouselItem['link'] ?? '');
                    $carouselAlt = (string)($carouselItem['alt'] ?? '');
                    ?>
                    <div class="carousel-slide" data-slide-index="<?= (int)$carouselIndex ?>">
                        <div class="carousel-media">
                            <?php if ($carouselType === 'video'): ?>
                                <video class="carousel-video" src="<?= e($carouselSrc) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
                            <?php else: ?>
                                <img src="<?= e($carouselSrc) ?>" alt="<?= e($carouselAlt !== '' ? $carouselAlt : 'Imagem do carousel') ?>" loading="lazy">
                            <?php endif; ?>
                            <?php if ($carouselLink !== ''): ?>
                                <a class="carousel-slide-link" href="<?= e($carouselLink) ?>" target="_blank" rel="noopener">
                                    <span class="carousel-link-overlay" aria-hidden="true"><span class="carousel-link-btn">Acessar</span></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="carousel-nav carousel-next" aria-label="Proximo slide">
                <span aria-hidden="true">&rsaquo;</span>
            </button>
        </div>
        <div class="carousel-dots" role="tablist" aria-label="Navegacao do carousel">
            <?php foreach ($carouselItems as $carouselIndex => $carouselItem): ?>
                <button type="button" class="carousel-dot" data-slide-index="<?= (int)$carouselIndex ?>" aria-label="Ir para o slide <?= (int)$carouselIndex + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php elseif ($showCarousel): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-carousel-block<?= $hasImage ? ' image-bg' : '' ?>" style="<?= $style ?><?= $frameVars ?>">
    <div class="section-carousel-wrap reveal anim-<?= e($effectiveSectionAnim) ?>" style="color: <?= e($textColor) ?>; font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
        <h2 class="section-carousel-title" style="font-family: <?= e($titleFontFamily) ?>; font-size: clamp(1.6rem, 3.3vw, <?= $titleSize ?>px);"><?= e($section['title'] ?? 'Secao') ?></h2>
        <?php if (trim(strip_tags($contentRaw)) !== ''): ?>
            <div class="section-carousel-desc" style="font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
                <?php if (strip_tags($contentRaw) === $contentRaw): ?>
                    <p><?= nl2br(e($contentRaw)) ?></p>
                <?php else: ?>
                    <?= sanitize_rich_text($contentRaw) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="carousel" data-carousel="true">
            <div class="carousel-viewport">
                <button type="button" class="carousel-nav carousel-prev" aria-label="Slide anterior">
                    <span aria-hidden="true">&lsaquo;</span>
                </button>
                <div class="carousel-track">
                    <?php foreach ($carouselItems as $carouselIndex => $carouselItem): ?>
                        <?php
                        $carouselType = (string)($carouselItem['type'] ?? 'image');
                        $carouselSrc = (string)($carouselItem['src'] ?? '');
                        $carouselLink = (string)($carouselItem['link'] ?? '');
                        $carouselAlt = (string)($carouselItem['alt'] ?? '');
                        ?>
                        <div class="carousel-slide" data-slide-index="<?= (int)$carouselIndex ?>">
                            <div class="carousel-media">
                                <?php if ($carouselType === 'video'): ?>
                                    <video class="carousel-video" src="<?= e($carouselSrc) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
                                <?php else: ?>
                                    <img src="<?= e($carouselSrc) ?>" alt="<?= e($carouselAlt !== '' ? $carouselAlt : 'Imagem do carousel') ?>" loading="lazy">
                                <?php endif; ?>
                                <?php if ($carouselLink !== ''): ?>
                                    <a class="carousel-slide-link" href="<?= e($carouselLink) ?>" target="_blank" rel="noopener">
                                        <span class="carousel-link-overlay" aria-hidden="true"><span class="carousel-link-btn">Acessar</span></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="carousel-nav carousel-next" aria-label="Proximo slide">
                    <span aria-hidden="true">&rsaquo;</span>
                </button>
            </div>
            <div class="carousel-dots" role="tablist" aria-label="Navegacao do carousel">
                <?php foreach ($carouselItems as $carouselIndex => $carouselItem): ?>
                    <button type="button" class="carousel-dot" data-slide-index="<?= (int)$carouselIndex ?>" aria-label="Ir para o slide <?= (int)$carouselIndex + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php elseif ($isVideoFull): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-video-full" style="background-color: <?= e($section['background_color'] ?? '#ffffff') ?>;<?= $frameVars ?>">
    <div class="section-video-full-wrap reveal anim-<?= e($effectiveSectionAnim) ?>">
        <video class="section-video-full-media" src="<?= e($sectionVideo) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
    </div>
</section>
<?php elseif ($isVideoBackground): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block section-video-background" style="background-color: <?= e($section['background_color'] ?? '#ffffff') ?>;<?= $frameVars ?>">
    <video class="section-bg-video" src="<?= e($sectionVideo) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
    <div class="overlay-content reveal anim-<?= e($effectiveSectionAnim) ?>" style="color: <?= e($textColor) ?>; font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
        <h2 style="font-family: <?= e($titleFontFamily) ?>; font-size: clamp(1.6rem, 3.3vw, <?= $titleSize ?>px);"><?= e($section['title'] ?? 'Secao') ?></h2>
        <div class="section-rich-text" style="font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
            <?php if (strip_tags($contentRaw) === $contentRaw): ?>
                <p><?= nl2br(e($contentRaw)) ?></p>
            <?php else: ?>
                <?= sanitize_rich_text($contentRaw) ?>
            <?php endif; ?>
        </div>
        <?php if ($showYouTube): ?>
            <div class="section-extra-box">
                <iframe class="section-embed-video" src="<?= e($youtubeEmbedUrl) ?>" title="Video YouTube" loading="lazy" allowfullscreen></iframe>
            </div>
        <?php endif; ?>
        <?php if ($showMap): ?>
            <div class="section-extra-box">
                <iframe class="section-embed-map" src="<?= e($mapEmbedUrl) ?>" title="Mapa da localizacao" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        <?php endif; ?>
        <?php if ($showLinkedGallery): ?>
            <div class="section-linked-gallery">
                <?php foreach ($linkedImages as $linkedImage): ?>
                    <?php if ($linkedImage['link'] !== ''): ?>
                        <a class="linked-gallery-item" href="<?= e($linkedImage['link']) ?>" target="_blank" rel="noopener">
                            <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                        </a>
                    <?php else: ?>
                        <div class="linked-gallery-item">
                            <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($showContactForm): ?>
            <div class="section-contact-box">
                <h3 style="font-family: <?= e($titleFontFamily) ?>;"><?= e($contactFormTitle) ?></h3>
                <form class="section-contact-form js-mailto-form" action="mailto:<?= e($contactDestination) ?>" method="post" enctype="text/plain" data-destination="<?= e($contactDestination) ?>" data-section-title="<?= e($section['title'] ?? 'Contato') ?>">
                    <label>Nome
                        <input type="text" name="name" required>
                    </label>
                    <label>Telefone
                        <input type="text" name="phone" required>
                    </label>
                    <label>E-mail
                        <input type="email" name="email" required>
                    </label>
                    <label>Mensagem
                        <textarea name="message" rows="4" required></textarea>
                    </label>
                    <button type="submit"><?= e($contactButtonText) ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php elseif (($layoutMode === 'split-left' || $layoutMode === 'split-right') && ($hasSplitImage || $isVideoSplit) || $isVideoSplit): ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block split-layout <?= e($effectiveSplitLayout) ?> split-size-<?= e($splitSize) ?> split-fit-<?= e($splitFit) ?><?= $splitHasCustomWidth ? ' has-custom-width' : '' ?><?= $splitHasCustomHeight ? ' has-custom-height' : '' ?>" style="<?= $splitSectionStyle ?><?= $frameVars ?>">
    <div class="split-wrap reveal anim-<?= e($effectiveSectionAnim) ?>">
        <div class="split-media">
            <?php if ($isVideoSplit): ?>
                <video class="split-video" src="<?= e($sectionVideo) ?>" autoplay muted loop playsinline preload="metadata" disablepictureinpicture noremoteplayback></video>
            <?php else: ?>
                <img src="<?= e($section['split_image']) ?>" alt="Imagem da secao <?= e($section['title'] ?? 'Secao') ?>">
            <?php endif; ?>
        </div>
        <div class="split-text overlay-content" style="color: <?= e($textColor) ?>; font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
            <h2 style="font-family: <?= e($titleFontFamily) ?>; font-size: clamp(1.6rem, 3.3vw, <?= $titleSize ?>px);"><?= e($section['title'] ?? 'Secao') ?></h2>
            <div class="section-rich-text" style="font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
                <?php if (strip_tags($contentRaw) === $contentRaw): ?>
                    <p><?= nl2br(e($contentRaw)) ?></p>
                <?php else: ?>
                    <?= sanitize_rich_text($contentRaw) ?>
                <?php endif; ?>
            </div>
            <?php if ($showYouTube): ?>
                <div class="section-extra-box">
                    <iframe class="section-embed-video" src="<?= e($youtubeEmbedUrl) ?>" title="Video YouTube" loading="lazy" allowfullscreen></iframe>
                </div>
            <?php endif; ?>
            <?php if ($showMap): ?>
                <div class="section-extra-box">
                    <iframe class="section-embed-map" src="<?= e($mapEmbedUrl) ?>" title="Mapa da localizacao" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            <?php endif; ?>
            <?php if ($showLinkedGallery): ?>
                <div class="section-linked-gallery">
                    <?php foreach ($linkedImages as $linkedImage): ?>
                        <?php if ($linkedImage['link'] !== ''): ?>
                            <a class="linked-gallery-item" href="<?= e($linkedImage['link']) ?>" target="_blank" rel="noopener">
                                <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                            </a>
                        <?php else: ?>
                            <div class="linked-gallery-item">
                                <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($showContactForm): ?>
                <div class="section-contact-box">
                    <h3 style="font-family: <?= e($titleFontFamily) ?>;"><?= e($contactFormTitle) ?></h3>
                    <form class="section-contact-form js-mailto-form" action="mailto:<?= e($contactDestination) ?>" method="post" enctype="text/plain" data-destination="<?= e($contactDestination) ?>" data-section-title="<?= e($section['title'] ?? 'Contato') ?>">
                        <label>Nome
                            <input type="text" name="name" required>
                        </label>
                        <label>Telefone
                            <input type="text" name="phone" required>
                        </label>
                        <label>E-mail
                            <input type="email" name="email" required>
                        </label>
                        <label>Mensagem
                            <textarea name="message" rows="4" required></textarea>
                        </label>
                        <button type="submit"><?= e($contactButtonText) ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php else: ?>
<section id="<?= e($section['slug'] ?? '') ?>" class="content-block<?= $hasImage ? ' image-bg' : '' ?>" style="<?= $style ?><?= $frameVars ?>">
    <div class="overlay-content reveal anim-<?= e($effectiveSectionAnim) ?>" style="color: <?= e($textColor) ?>; font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
        <h2 style="font-family: <?= e($titleFontFamily) ?>; font-size: clamp(1.6rem, 3.3vw, <?= $titleSize ?>px);"><?= e($section['title'] ?? 'Secao') ?></h2>
        <div class="section-rich-text" style="font-family: <?= e($fontFamily) ?>; font-size: <?= $fontSize ?>px;">
            <?php if (strip_tags($contentRaw) === $contentRaw): ?>
                <p><?= nl2br(e($contentRaw)) ?></p>
            <?php else: ?>
                <?= sanitize_rich_text($contentRaw) ?>
            <?php endif; ?>
        </div>
        <?php if ($showYouTube): ?>
            <div class="section-extra-box">
                <iframe class="section-embed-video" src="<?= e($youtubeEmbedUrl) ?>" title="Video YouTube" loading="lazy" allowfullscreen></iframe>
            </div>
        <?php endif; ?>
        <?php if ($showMap): ?>
            <div class="section-extra-box">
                <iframe class="section-embed-map" src="<?= e($mapEmbedUrl) ?>" title="Mapa da localizacao" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        <?php endif; ?>
        <?php if ($showLinkedGallery): ?>
            <div class="section-linked-gallery">
                <?php foreach ($linkedImages as $linkedImage): ?>
                    <?php if ($linkedImage['link'] !== ''): ?>
                        <a class="linked-gallery-item" href="<?= e($linkedImage['link']) ?>" target="_blank" rel="noopener">
                            <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                        </a>
                    <?php else: ?>
                        <div class="linked-gallery-item">
                            <img src="<?= e($linkedImage['image']) ?>" alt="<?= e($linkedImage['alt']) ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($showContactForm): ?>
            <div class="section-contact-box">
                <h3 style="font-family: <?= e($titleFontFamily) ?>;"><?= e($contactFormTitle) ?></h3>
                <form class="section-contact-form js-mailto-form" action="mailto:<?= e($contactDestination) ?>" method="post" enctype="text/plain" data-destination="<?= e($contactDestination) ?>" data-section-title="<?= e($section['title'] ?? 'Contato') ?>">
                    <label>Nome
                        <input type="text" name="name" required>
                    </label>
                    <label>Telefone
                        <input type="text" name="phone" required>
                    </label>
                    <label>E-mail
                        <input type="email" name="email" required>
                    </label>
                    <label>Mensagem
                        <textarea name="message" rows="4" required></textarea>
                    </label>
                    <button type="submit"><?= e($contactButtonText) ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php endforeach; ?>

<footer id="contatos" class="footer" style="font-family: <?= e($footerFont) ?>; font-size: <?= $footerSize ?>px;">
    <?php
    $whatsappNumber = preg_replace('/\D+/', '', (string)($meta['whatsapp'] ?? ''));
    $socialItems = [
        [
            'enabled' => !empty($meta['social_facebook_enabled']),
            'url' => (string)($meta['social_facebook_url'] ?? ''),
            'label' => 'Facebook',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.6 1.6-1.6h1.7V3.8c-.3 0-1.4-.1-2.6-.1-2.6 0-4.3 1.6-4.3 4.5V10H7.8v3h2.7v8h3z"/></svg>',
        ],
        [
            'enabled' => !empty($meta['social_instagram_enabled']),
            'url' => (string)($meta['social_instagram_url'] ?? ''),
            'label' => 'Instagram',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm0 2a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H7zm10.5 1.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM12 7a5 5 0 110 10 5 5 0 010-10zm0 2a3 3 0 100 6 3 3 0 000-6z"/></svg>',
        ],
        [
            'enabled' => !empty($meta['social_linkedin_enabled']),
            'url' => (string)($meta['social_linkedin_url'] ?? ''),
            'label' => 'LinkedIn',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5A2.5 2.5 0 107.5 6a2.5 2.5 0 00-2.52-2.5zM3.5 8h4v12h-4V8zm7 0h3.8v1.7h.1c.5-1 1.9-2.1 3.9-2.1 4.2 0 5 2.8 5 6.4V20h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V20h-4V8z"/></svg>',
        ],
        [
            'enabled' => !empty($meta['social_behance_enabled']),
            'url' => (string)($meta['social_behance_url'] ?? ''),
            'label' => 'Behance',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h6.7c2.6 0 4.3 1.4 4.3 3.6 0 1.5-.8 2.6-2.1 3.1 1.8.4 2.8 1.8 2.8 3.6C14.7 20 12.8 22 9.8 22H3V7zm6.2 6c1.1 0 1.8-.6 1.8-1.6s-.7-1.5-1.8-1.5H6v3.1h3.2zm.3 6c1.4 0 2.2-.7 2.2-1.9 0-1.1-.8-1.8-2.2-1.8H6V19h3.5zM16 9h5v1.4h-5V9zm5.8 7.4h-6.2c.2 1.6 1.2 2.4 2.8 2.4 1 0 1.8-.3 2.5-1l1.5 1.3c-.9 1.1-2.4 1.8-4.2 1.8-3.2 0-5.3-2.1-5.3-5.2 0-3.2 2.1-5.3 5.2-5.3 3 0 4.9 2.1 4.9 5.3v.7zM15.7 14.6h3.8c-.1-1.4-.9-2.2-1.9-2.2-1.2 0-1.9.8-2 2.2z"/></svg>',
        ],
        [
            'enabled' => !empty($meta['social_youtube_enabled']),
            'url' => (string)($meta['social_youtube_url'] ?? ''),
            'label' => 'YouTube',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 12s0-3.2-.4-4.7c-.2-.8-.9-1.5-1.7-1.7C19.3 5 12 5 12 5s-7.3 0-8.9.6c-.8.2-1.5.9-1.7 1.7C1 8.8 1 12 1 12s0 3.2.4 4.7c.2.8.9 1.5 1.7 1.7C4.7 19 12 19 12 19s7.3 0 8.9-.6c.8-.2 1.5-.9 1.7-1.7.4-1.5.4-4.7.4-4.7zM10 15.5v-7l6 3.5-6 3.5z"/></svg>',
        ],
    ];
    if ($whatsappNumber !== '') {
        $socialItems[] = [
            'enabled' => true,
            'url' => 'https://wa.me/' . $whatsappNumber,
            'label' => 'WhatsApp',
            'class' => 'social-icon-whatsapp',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11 11 0 003.2 16.8L2 22l5.3-1.1A11 11 0 1020.5 3.5zM12 20a8 8 0 01-4.1-1.1l-.3-.2-3.1.7.7-3-.2-.3A8 8 0 1112 20zm4.5-5.7c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.7 1-.1.1-.3.2-.5.1-1.4-.7-2.3-1.3-3.2-3-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.2.2-.4.1-.1 0-.3 0-.4 0-.1-.5-1.3-.7-1.8-.2-.4-.3-.4-.5-.4h-.4c-.1 0-.4 0-.5.2-.2.2-.8.8-.8 2s.8 2.4.9 2.5c.1.2 1.6 2.6 4 3.6 2.3 1 2.3.7 2.7.7.4-.1 1.4-.6 1.6-1.1.2-.5.2-.9.1-1 0-.1-.2-.2-.5-.3z"/></svg>',
        ];
    }
    ?>
    <div class="footer-col reveal anim-none is-visible">
        <h3><?= e($meta['site_name'] ?? 'Minha Empresa') ?></h3>
        <div><strong>Telefone:</strong> <?= e($meta['contact_phone'] ?? '') ?></div>
        <div><strong>E-mail:</strong> <?= e($meta['contact_email'] ?? '') ?></div>
        <div><strong>Endereco:</strong> <?= e($meta['address'] ?? '') ?></div>
    </div>
    <div class="footer-col reveal anim-none is-visible">
        <h3>Links rapidos</h3>
        <div><a href="#inicio">Inicio</a></div>
        <?php foreach ($sections as $section): ?>
            <div><a href="#<?= e($section['slug'] ?? '') ?>"><?= e($section['menu_label'] ?? ($section['title'] ?? 'Secao')) ?></a></div>
        <?php endforeach; ?>
    </div>
    <div class="footer-col reveal anim-none is-visible">
        <h3>Siga-nos nas redes sociais</h3>
        <div class="social-list">
            <?php foreach ($socialItems as $social): ?>
                <?php if ($social['enabled'] && $social['url'] !== ''): ?>
                    <a class="social-icon <?= e((string)($social['class'] ?? '')) ?>" href="<?= e($social['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($social['label']) ?>">
                        <?= $social['icon'] ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="admin-link"><a href="admin/login.php">Dashboard</a></div>
    </div>
</footer>

<script src="assets/site.js"></script>
</body>
</html>
