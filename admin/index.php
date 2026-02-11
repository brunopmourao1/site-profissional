<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

$data = load_site_data();
$message = '';
$error = '';
$status = sanitize_text((string)($_GET['status'] ?? ''));
$flashMessage = sanitize_text((string)($_GET['message'] ?? ''));
if ($status === 'success' && $flashMessage !== '') {
    $message = $flashMessage;
}
if ($status === 'error' && $flashMessage !== '') {
    $error = $flashMessage;
}

if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0775, true);
}

function find_section_index(array $sections, int $id): ?int
{
    foreach ($sections as $index => $section) {
        if ((int)($section['id'] ?? 0) === $id) {
            return $index;
        }
    }

    return null;
}

function upload_error_message(int $code): string
{
    $uploadMax = (string)ini_get('upload_max_filesize');
    $postMax = (string)ini_get('post_max_size');
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Arquivo muito grande para upload. Limites atuais do servidor: upload_max_filesize=' . $uploadMax . ' e post_max_size=' . $postMax . '.',
        UPLOAD_ERR_PARTIAL => 'Upload incompleto. Tente novamente.',
        UPLOAD_ERR_NO_TMP_DIR => 'Servidor sem pasta temporaria para upload.',
        UPLOAD_ERR_CANT_WRITE => 'Servidor sem permissao para gravar arquivo.',
        UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensao do PHP.',
        default => 'Falha no upload do arquivo.',
    };
}

function detect_uploaded_mime_type(string $tmpPath): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string)finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            if ($mime !== '') {
                return $mime;
            }
        }
    }

    if (function_exists('mime_content_type')) {
        $mime = (string)mime_content_type($tmpPath);
        if ($mime !== '') {
            return $mime;
        }
    }

    $imageInfo = @getimagesize($tmpPath);
    if (is_array($imageInfo) && isset($imageInfo['mime']) && is_string($imageInfo['mime'])) {
        return $imageInfo['mime'];
    }

    return '';
}

function resolve_section_mode_and_feature(string $sectionFunction, string $splitMediaType, string $backgroundMediaType): array
{
    $sectionFunction = normalize_section_function($sectionFunction);
    $splitMediaType = normalize_media_type($splitMediaType);
    $backgroundMediaType = normalize_media_type($backgroundMediaType);

    $sectionMode = 'text';
    $sectionFeature = 'none';
    $cardsStyle = 'media';

    if ($sectionFunction === 'split_media') {
        $sectionMode = $splitMediaType === 'video' ? 'video_split' : 'text';
    } elseif ($sectionFunction === 'background_media') {
        $sectionMode = $backgroundMediaType === 'video' ? 'video_background' : 'text';
    } elseif ($sectionFunction === 'cards_media') {
        $sectionFeature = 'cards';
        $cardsStyle = 'media';
    } elseif ($sectionFunction === 'cards_text') {
        $sectionFeature = 'cards';
        $cardsStyle = 'text';
    } elseif (in_array($sectionFunction, ['linked_gallery', 'youtube', 'map', 'contact_form'], true)) {
        $sectionFeature = $sectionFunction;
    }

    return [$sectionMode, $sectionFeature, $cardsStyle];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    $returnPanel = sanitize_text((string)($_POST['return_panel'] ?? 'geral'));
    if (!in_array($returnPanel, ['geral', 'criar', 'secoes', 'acesso'], true)) {
        $returnPanel = 'geral';
    }
    $returnSectionId = (int)($_POST['return_section_id'] ?? 0);

    if ($action === 'update_meta') {
        $data['meta']['site_name'] = sanitize_text((string)($_POST['site_name'] ?? ''));
        $data['meta']['headline'] = sanitize_text((string)($_POST['headline'] ?? ''));
        $data['meta']['hero_text_color'] = sanitize_text((string)($_POST['hero_text_color'] ?? '#f6f2eb'));
        $data['meta']['intro'] = sanitize_text((string)($_POST['intro'] ?? ''));
        $data['meta']['brand_font'] = sanitize_text((string)($_POST['brand_font'] ?? 'sora'));
        $data['meta']['brand_size'] = normalize_text_size($_POST['brand_size'] ?? 18);
        $data['meta']['headline_font'] = sanitize_text((string)($_POST['headline_font'] ?? 'sora'));
        $data['meta']['headline_size'] = normalize_text_size($_POST['headline_size'] ?? 42);
        $data['meta']['intro_font'] = sanitize_text((string)($_POST['intro_font'] ?? 'manrope'));
        $data['meta']['intro_size'] = normalize_text_size($_POST['intro_size'] ?? 17);
        $data['meta']['footer_font'] = sanitize_text((string)($_POST['footer_font'] ?? 'manrope'));
        $data['meta']['footer_size'] = normalize_text_size($_POST['footer_size'] ?? 16);
        $data['meta']['animation_style'] = sanitize_text((string)($_POST['animation_style'] ?? 'slide'));
        $data['meta']['hero_motion'] = sanitize_text((string)($_POST['hero_motion'] ?? 'soft'));
        $heroFunction = sanitize_text((string)($_POST['hero_function'] ?? 'text'));
        if (!in_array($heroFunction, ['text', 'video', 'image'], true)) {
            $heroFunction = 'text';
        }
        $heroVideoVariant = sanitize_text((string)($_POST['hero_video_variant'] ?? 'background_text'));
        if (!in_array($heroVideoVariant, ['background_text', 'split_text', 'full'], true)) {
            $heroVideoVariant = 'background_text';
        }
        $heroImageVariant = sanitize_text((string)($_POST['hero_image_variant'] ?? 'background_text'));
        if (!in_array($heroImageVariant, ['background_text', 'split_text', 'full'], true)) {
            $heroImageVariant = 'background_text';
        }
        $heroMode = 'text';
        if ($heroFunction === 'video') {
            $heroMode = $heroVideoVariant === 'full'
                ? 'video_full'
                : ($heroVideoVariant === 'split_text' ? 'video_split' : 'video_background');
        } elseif ($heroFunction === 'image') {
            $heroMode = $heroImageVariant === 'full'
                ? 'image_full'
                : ($heroImageVariant === 'split_text' ? 'image_split' : 'image_background');
        }
        $data['meta']['hero_mode'] = $heroMode;
        $heroLayout = sanitize_text((string)($_POST['hero_layout'] ?? 'split-right'));
        if (!in_array($heroLayout, ['split-left', 'split-right'], true)) {
            $heroLayout = 'split-right';
        }
        $data['meta']['hero_layout'] = $heroLayout;
        $heroSplitSize = sanitize_text((string)($_POST['hero_split_size'] ?? 'medium'));
        if (!in_array($heroSplitSize, ['small', 'medium', 'large'], true)) {
            $heroSplitSize = 'medium';
        }
        $data['meta']['hero_split_size'] = $heroSplitSize;
        $data['meta']['hero_split_width'] = normalize_dimension_size($_POST['hero_split_width'] ?? 0);
        $data['meta']['hero_split_height'] = normalize_dimension_size($_POST['hero_split_height'] ?? 0);
        $heroSplitFit = sanitize_text((string)($_POST['hero_split_fit'] ?? 'cover'));
        if (!in_array($heroSplitFit, ['cover', 'contain'], true)) {
            $heroSplitFit = 'cover';
        }
        $data['meta']['hero_split_fit'] = $heroSplitFit;
        $data['meta']['primary_color'] = sanitize_text((string)($_POST['primary_color'] ?? '#113946'));
        $data['meta']['secondary_color'] = sanitize_text((string)($_POST['secondary_color'] ?? '#bca37f'));
        $data['meta']['contact_phone'] = sanitize_text((string)($_POST['contact_phone'] ?? ''));
        $data['meta']['contact_email'] = sanitize_text((string)($_POST['contact_email'] ?? ''));
        $data['meta']['address'] = sanitize_text((string)($_POST['address'] ?? ''));
        $data['meta']['whatsapp'] = sanitize_text((string)($_POST['whatsapp'] ?? ''));
        $data['meta']['social_facebook_enabled'] = isset($_POST['social_facebook_enabled']);
        $data['meta']['social_facebook_url'] = sanitize_text((string)($_POST['social_facebook_url'] ?? ''));
        $data['meta']['social_instagram_enabled'] = isset($_POST['social_instagram_enabled']);
        $data['meta']['social_instagram_url'] = sanitize_text((string)($_POST['social_instagram_url'] ?? ''));
        $data['meta']['social_linkedin_enabled'] = isset($_POST['social_linkedin_enabled']);
        $data['meta']['social_linkedin_url'] = sanitize_text((string)($_POST['social_linkedin_url'] ?? ''));
        $data['meta']['social_behance_enabled'] = isset($_POST['social_behance_enabled']);
        $data['meta']['social_behance_url'] = sanitize_text((string)($_POST['social_behance_url'] ?? ''));
        $data['meta']['social_youtube_enabled'] = isset($_POST['social_youtube_enabled']);
        $data['meta']['social_youtube_url'] = sanitize_text((string)($_POST['social_youtube_url'] ?? ''));
        $data['meta']['logo_alt'] = sanitize_text((string)($_POST['logo_alt'] ?? 'Logo da empresa'));

        $currentLogo = (string)($data['meta']['logo_image'] ?? '');
        if (isset($_POST['remove_logo']) && $currentLogo !== '') {
            $localPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentLogo);
            if (is_file($localPath)) {
                @unlink($localPath);
            }
            $currentLogo = '';
        }

        $logoKey = 'logo_image';
        if (isset($_FILES[$logoKey]) && is_array($_FILES[$logoKey])) {
            $uploadError = (int)($_FILES[$logoKey]['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($uploadError !== UPLOAD_ERR_OK && $uploadError !== UPLOAD_ERR_NO_FILE) {
                $error = upload_error_message($uploadError);
            }
        }

        if ($error === '' && isset($_FILES[$logoKey]) && is_array($_FILES[$logoKey]) && ($_FILES[$logoKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)$_FILES[$logoKey]['tmp_name'];
            $original = (string)$_FILES[$logoKey]['name'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'text/plain', 'text/xml', 'application/xml'];

            if (in_array($ext, $allowed, true)) {
                $mime = detect_uploaded_mime_type($tmpName);

                if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                    $error = 'Logo invalida. Envie JPG, PNG, WEBP ou SVG.';
                } elseif (!is_uploaded_file($tmpName)) {
                    $error = 'Arquivo de upload invalido.';
                } elseif (!is_writable(UPLOADS_DIR)) {
                    $error = 'A pasta uploads/ nao tem permissao de escrita.';
                } else {
                    $filename = 'logo-' . time() . '.' . $ext;
                    $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;

                    if (move_uploaded_file($tmpName, $destination)) {
                        if ($currentLogo !== '') {
                            $oldPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentLogo);
                            if (is_file($oldPath)) {
                                @unlink($oldPath);
                            }
                        }
                        $currentLogo = UPLOADS_URL . '/' . $filename;
                    } else {
                        $error = 'Falha ao salvar logo em uploads/.';
                    }
                }
            } else {
                $error = 'Formato de logo invalido. Use JPG, PNG, WEBP ou SVG.';
            }
        }

        $data['meta']['logo_image'] = $currentLogo;

        $currentHeroVideo = (string)($data['meta']['hero_video'] ?? '');
        if (isset($_POST['remove_hero_video']) && $currentHeroVideo !== '') {
            $heroPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentHeroVideo);
            if (is_file($heroPath)) {
                @unlink($heroPath);
            }
            $currentHeroVideo = '';
        }

        $heroVideoKey = 'hero_video';
        if ($error === '' && isset($_FILES[$heroVideoKey]) && is_array($_FILES[$heroVideoKey])) {
            $videoUploadError = (int)($_FILES[$heroVideoKey]['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($videoUploadError !== UPLOAD_ERR_OK && $videoUploadError !== UPLOAD_ERR_NO_FILE) {
                $error = upload_error_message($videoUploadError);
            }
        }

        if ($error === '' && isset($_FILES[$heroVideoKey]) && is_array($_FILES[$heroVideoKey]) && ($_FILES[$heroVideoKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)$_FILES[$heroVideoKey]['tmp_name'];
            $original = (string)$_FILES[$heroVideoKey]['name'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['mp4', 'webm', 'ogg'];
            $allowedMime = ['video/mp4', 'video/webm', 'video/ogg', 'video/x-m4v', 'application/mp4', 'application/octet-stream'];
            if (in_array($ext, $allowed, true)) {
                $mime = detect_uploaded_mime_type($tmpName);
                if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                    $error = 'Video invalido. Envie MP4, WEBM ou OGG.';
                } elseif (!is_uploaded_file($tmpName)) {
                    $error = 'Arquivo de video invalido.';
                } elseif (!is_writable(UPLOADS_DIR)) {
                    $error = 'A pasta uploads/ nao tem permissao de escrita.';
                } else {
                    $filename = 'hero-' . time() . '.' . $ext;
                    $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                    if (move_uploaded_file($tmpName, $destination)) {
                        if ($currentHeroVideo !== '') {
                            $oldPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentHeroVideo);
                            if (is_file($oldPath)) {
                                @unlink($oldPath);
                            }
                        }
                        $currentHeroVideo = UPLOADS_URL . '/' . $filename;
                    } else {
                        $error = 'Falha ao salvar video principal em uploads/.';
                    }
                }
            } else {
                $error = 'Formato de video invalido. Use MP4, WEBM ou OGG.';
            }
        }

        $data['meta']['hero_video'] = $currentHeroVideo;

        $currentHeroImage = (string)($data['meta']['hero_image'] ?? '');
        if (isset($_POST['remove_hero_image']) && $currentHeroImage !== '') {
            $heroImagePath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentHeroImage);
            if (is_file($heroImagePath)) {
                @unlink($heroImagePath);
            }
            $currentHeroImage = '';
        }

        $heroImageKey = 'hero_image';
        if ($error === '' && isset($_FILES[$heroImageKey]) && is_array($_FILES[$heroImageKey])) {
            $heroImageUploadError = (int)($_FILES[$heroImageKey]['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($heroImageUploadError !== UPLOAD_ERR_OK && $heroImageUploadError !== UPLOAD_ERR_NO_FILE) {
                $error = upload_error_message($heroImageUploadError);
            }
        }

        if ($error === '' && isset($_FILES[$heroImageKey]) && is_array($_FILES[$heroImageKey]) && ($_FILES[$heroImageKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)$_FILES[$heroImageKey]['tmp_name'];
            $original = (string)$_FILES[$heroImageKey]['name'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($ext, $allowed, true)) {
                $mime = detect_uploaded_mime_type($tmpName);
                if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                    $error = 'Imagem principal invalida. Envie JPG, PNG ou WEBP.';
                } elseif (!is_uploaded_file($tmpName)) {
                    $error = 'Arquivo de imagem principal invalido.';
                } elseif (!is_writable(UPLOADS_DIR)) {
                    $error = 'A pasta uploads/ nao tem permissao de escrita.';
                } else {
                    $filename = 'hero-image-' . time() . '.' . $ext;
                    $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                    if (move_uploaded_file($tmpName, $destination)) {
                        if ($currentHeroImage !== '') {
                            $oldPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentHeroImage);
                            if (is_file($oldPath)) {
                                @unlink($oldPath);
                            }
                        }
                        $currentHeroImage = UPLOADS_URL . '/' . $filename;
                    } else {
                        $error = 'Falha ao salvar imagem principal em uploads/.';
                    }
                }
            } else {
                $error = 'Formato de imagem principal invalido. Use JPG, PNG ou WEBP.';
            }
        }

        $data['meta']['hero_image'] = $currentHeroImage;

        save_site_data($data);
        if ($error === '') {
            $message = 'Dados gerais atualizados com sucesso.';
        }
    }

    if ($action === 'add_section') {
        $title = sanitize_text((string)($_POST['title'] ?? 'Nova secao'));
        $slug = slugify((string)($_POST['slug'] ?? $title));

        $sectionFunction = normalize_section_function($_POST['section_function'] ?? 'basic_text');
        $splitMediaType = normalize_media_type($_POST['split_media_type'] ?? 'image');
        $backgroundMediaType = normalize_media_type($_POST['background_media_type'] ?? 'image');
        [$sectionMode, $sectionFeature, $cardsStyle] = resolve_section_mode_and_feature($sectionFunction, $splitMediaType, $backgroundMediaType);
        $linkedImagesLayout = sanitize_text((string)($_POST['linked_images_layout'] ?? 'boxed'));
        if (!in_array($linkedImagesLayout, ['boxed', 'direct'], true)) {
            $linkedImagesLayout = 'boxed';
        }
        $layoutMode = sanitize_text((string)($_POST['layout_mode'] ?? 'background'));
        if (!in_array($layoutMode, ['background', 'split-left', 'split-right'], true)) {
            $layoutMode = 'background';
        }
        if ($sectionFunction === 'split_media') {
            if (!in_array($layoutMode, ['split-left', 'split-right'], true)) {
                $layoutMode = 'split-right';
            }
        } else {
            $layoutMode = 'background';
        }
        $youtubeEnabled = $sectionFeature === 'youtube';
        $mapEnabled = $sectionFeature === 'map';
        $contactEnabled = $sectionFeature === 'contact_form';

        $data['sections'][] = [
            'id' => next_section_id($data['sections']),
            'title' => $title,
            'slug' => $slug,
            'content' => sanitize_rich_text((string)($_POST['content'] ?? '')),
            'text_color' => sanitize_text((string)($_POST['text_color'] ?? '#102133')),
            'title_font' => sanitize_text((string)($_POST['title_font'] ?? 'sora')),
            'title_size' => normalize_text_size($_POST['title_size'] ?? 40),
            'text_font' => sanitize_text((string)($_POST['text_font'] ?? 'manrope')),
            'text_size' => normalize_text_size($_POST['text_size'] ?? 18),
            'layout_mode' => $layoutMode,
            'split_image' => '',
            'split_size' => sanitize_text((string)($_POST['split_size'] ?? 'medium')),
            'split_fit' => sanitize_text((string)($_POST['split_fit'] ?? 'cover')),
            'split_width' => normalize_dimension_size($_POST['split_width'] ?? 0),
            'split_height' => normalize_dimension_size($_POST['split_height'] ?? 0),
            'section_animation' => sanitize_text((string)($_POST['section_animation'] ?? 'inherit')),
            'section_video' => '',
            'section_mode' => $sectionMode,
            'section_function' => $sectionFunction,
            'split_media_type' => $splitMediaType,
            'background_media_type' => $backgroundMediaType,
            'section_feature' => $sectionFeature,
            'youtube_enabled' => $youtubeEnabled,
            'youtube_url' => sanitize_text((string)($_POST['youtube_url'] ?? '')),
            'map_enabled' => $mapEnabled,
            'map_embed_url' => sanitize_text((string)($_POST['map_embed_url'] ?? '')),
            'contact_enabled' => $contactEnabled,
            'contact_form_title' => sanitize_text((string)($_POST['contact_form_title'] ?? 'Envie sua mensagem')),
            'contact_destination_email' => sanitize_text((string)($_POST['contact_destination_email'] ?? '')),
            'contact_button_text' => sanitize_text((string)($_POST['contact_button_text'] ?? 'Enviar')),
            'linked_images' => [],
            'linked_images_limit' => normalize_linked_images_limit($_POST['linked_images_limit'] ?? 6),
            'linked_images_layout' => $linkedImagesLayout,
            'cards_title' => sanitize_text((string)($_POST['cards_title'] ?? '')),
            'cards_style' => $cardsStyle,
            'cards_items' => [],
            'background_color' => sanitize_text((string)($_POST['background_color'] ?? '#ffffff')),
            'background_image' => '',
            'menu_label' => sanitize_text((string)($_POST['menu_label'] ?? $title)),
            'order' => count($data['sections']) + 1,
        ];

        $newSectionIndex = count($data['sections']) - 1;
        if ($data['sections'][$newSectionIndex]['contact_form_title'] === '') {
            $data['sections'][$newSectionIndex]['contact_form_title'] = 'Envie sua mensagem';
        }
        if ($data['sections'][$newSectionIndex]['contact_button_text'] === '') {
            $data['sections'][$newSectionIndex]['contact_button_text'] = 'Enviar';
        }

        save_site_data($data);
        $message = 'Nova aba criada e adicionada no menu.';
    }

    if ($action === 'update_section') {
        $id = (int)($_POST['id'] ?? 0);
        $index = find_section_index($data['sections'], $id);

        if ($index !== null) {
            $currentImage = (string)($data['sections'][$index]['background_image'] ?? '');
            $data['sections'][$index]['title'] = sanitize_text((string)($_POST['title'] ?? ''));
            $data['sections'][$index]['slug'] = slugify((string)($_POST['slug'] ?? ''));
            $data['sections'][$index]['content'] = sanitize_rich_text((string)($_POST['content'] ?? ''));
            $data['sections'][$index]['text_color'] = sanitize_text((string)($_POST['text_color'] ?? '#102133'));
            $data['sections'][$index]['title_font'] = sanitize_text((string)($_POST['title_font'] ?? 'sora'));
            $data['sections'][$index]['title_size'] = normalize_text_size($_POST['title_size'] ?? 40);
            $data['sections'][$index]['text_font'] = sanitize_text((string)($_POST['text_font'] ?? 'manrope'));
            $data['sections'][$index]['text_size'] = normalize_text_size($_POST['text_size'] ?? 18);
            $layoutMode = sanitize_text((string)($_POST['layout_mode'] ?? 'background'));
            if (!in_array($layoutMode, ['background', 'split-left', 'split-right'], true)) {
                $layoutMode = 'background';
            }
            $splitSize = sanitize_text((string)($_POST['split_size'] ?? 'medium'));
            if (!in_array($splitSize, ['small', 'medium', 'large'], true)) {
                $splitSize = 'medium';
            }
            $splitFit = sanitize_text((string)($_POST['split_fit'] ?? 'cover'));
            if (!in_array($splitFit, ['cover', 'contain'], true)) {
                $splitFit = 'cover';
            }
            $sectionAnim = sanitize_text((string)($_POST['section_animation'] ?? 'inherit'));
            if (!in_array($sectionAnim, ['inherit', 'slide', 'fade', 'zoom', 'lift', 'left', 'right', 'top', 'letters', 'none'], true)) {
                $sectionAnim = 'inherit';
            }
            $data['sections'][$index]['section_animation'] = $sectionAnim;
            $sectionFunction = normalize_section_function($_POST['section_function'] ?? 'basic_text');
            $splitMediaType = normalize_media_type($_POST['split_media_type'] ?? 'image');
            $backgroundMediaType = normalize_media_type($_POST['background_media_type'] ?? 'image');
            [$sectionMode, $sectionFeature, $cardsStyle] = resolve_section_mode_and_feature($sectionFunction, $splitMediaType, $backgroundMediaType);
            if ($sectionFunction === 'split_media') {
                if (!in_array($layoutMode, ['split-left', 'split-right'], true)) {
                    $layoutMode = 'split-right';
                }
            } else {
                $layoutMode = 'background';
            }
            $linkedImagesLayout = sanitize_text((string)($_POST['linked_images_layout'] ?? 'boxed'));
            if (!in_array($linkedImagesLayout, ['boxed', 'direct'], true)) {
                $linkedImagesLayout = 'boxed';
            }
            $data['sections'][$index]['layout_mode'] = $layoutMode;
            $data['sections'][$index]['split_size'] = $splitSize;
            $data['sections'][$index]['split_fit'] = $splitFit;
            $data['sections'][$index]['split_width'] = normalize_dimension_size($_POST['split_width'] ?? 0);
            $data['sections'][$index]['split_height'] = normalize_dimension_size($_POST['split_height'] ?? 0);
            $data['sections'][$index]['section_mode'] = $sectionMode;
            $data['sections'][$index]['section_function'] = $sectionFunction;
            $data['sections'][$index]['split_media_type'] = $splitMediaType;
            $data['sections'][$index]['background_media_type'] = $backgroundMediaType;
            $data['sections'][$index]['section_feature'] = $sectionFeature;
            $data['sections'][$index]['youtube_enabled'] = $sectionFeature === 'youtube';
            $data['sections'][$index]['youtube_url'] = sanitize_text((string)($_POST['youtube_url'] ?? ''));
            $data['sections'][$index]['map_enabled'] = $sectionFeature === 'map';
            $data['sections'][$index]['map_embed_url'] = sanitize_text((string)($_POST['map_embed_url'] ?? ''));
            $data['sections'][$index]['contact_enabled'] = $sectionFeature === 'contact_form';
            $data['sections'][$index]['contact_form_title'] = sanitize_text((string)($_POST['contact_form_title'] ?? 'Envie sua mensagem'));
            $data['sections'][$index]['contact_destination_email'] = sanitize_text((string)($_POST['contact_destination_email'] ?? ''));
            $data['sections'][$index]['contact_button_text'] = sanitize_text((string)($_POST['contact_button_text'] ?? 'Enviar'));
            $data['sections'][$index]['linked_images_limit'] = normalize_linked_images_limit($_POST['linked_images_limit'] ?? 6);
            $data['sections'][$index]['linked_images_layout'] = $linkedImagesLayout;
            $data['sections'][$index]['cards_title'] = sanitize_text((string)($_POST['cards_title'] ?? ''));
            $data['sections'][$index]['cards_style'] = $cardsStyle;
            if ($data['sections'][$index]['contact_form_title'] === '') {
                $data['sections'][$index]['contact_form_title'] = 'Envie sua mensagem';
            }
            if ($data['sections'][$index]['contact_button_text'] === '') {
                $data['sections'][$index]['contact_button_text'] = 'Enviar';
            }
            $data['sections'][$index]['background_color'] = sanitize_text((string)($_POST['background_color'] ?? '#ffffff'));
            $data['sections'][$index]['menu_label'] = sanitize_text((string)($_POST['menu_label'] ?? ''));

            if (isset($_POST['remove_image']) && $currentImage !== '') {
                $localPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentImage);
                if (is_file($localPath)) {
                    @unlink($localPath);
                }
                $currentImage = '';
            }

            $fileKey = 'background_image_' . $id;
            if (isset($_FILES[$fileKey]) && is_array($_FILES[$fileKey])) {
                $uploadError = (int)($_FILES[$fileKey]['error'] ?? UPLOAD_ERR_NO_FILE);
                if ($uploadError !== UPLOAD_ERR_OK && $uploadError !== UPLOAD_ERR_NO_FILE) {
                    $error = upload_error_message($uploadError);
                }
            }

            if ($error === '' && isset($_FILES[$fileKey]) && is_array($_FILES[$fileKey]) && ($_FILES[$fileKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $tmpName = (string)$_FILES[$fileKey]['tmp_name'];
                $original = (string)$_FILES[$fileKey]['name'];
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($ext, $allowed, true)) {
                    $mime = detect_uploaded_mime_type($tmpName);

                    if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                        $error = 'Arquivo invalido. Envie apenas imagens JPG, PNG ou WEBP.';
                    } elseif (!is_uploaded_file($tmpName)) {
                        $error = 'Arquivo de upload invalido.';
                    } elseif (!is_writable(UPLOADS_DIR)) {
                        $error = 'A pasta uploads/ nao tem permissao de escrita.';
                    } else {
                        $filename = 'secao-' . $id . '-' . time() . '.' . $ext;
                        $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;

                        if (move_uploaded_file($tmpName, $destination)) {
                            if ($currentImage !== '') {
                                $oldPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentImage);
                                if (is_file($oldPath)) {
                                    @unlink($oldPath);
                                }
                            }
                            $currentImage = UPLOADS_URL . '/' . $filename;
                        } else {
                            $error = 'Falha ao mover arquivo para uploads/.';
                        }
                    }
                } else {
                    $error = 'Formato de imagem invalido. Use JPG, PNG ou WEBP.';
                }
            }

            $data['sections'][$index]['background_image'] = $currentImage;

            if (isset($_POST['remove_split_image']) && !empty($data['sections'][$index]['split_image'])) {
                $splitLocalPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string)$data['sections'][$index]['split_image']);
                if (is_file($splitLocalPath)) {
                    @unlink($splitLocalPath);
                }
                $data['sections'][$index]['split_image'] = '';
            }

            if (isset($_POST['remove_section_video']) && !empty($data['sections'][$index]['section_video'])) {
                $videoPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string)$data['sections'][$index]['section_video']);
                if (is_file($videoPath)) {
                    @unlink($videoPath);
                }
                $data['sections'][$index]['section_video'] = '';
            }

            $splitFileKey = 'split_image_' . $id;
            if ($error === '' && isset($_FILES[$splitFileKey]) && is_array($_FILES[$splitFileKey])) {
                $splitUploadError = (int)($_FILES[$splitFileKey]['error'] ?? UPLOAD_ERR_NO_FILE);
                if ($splitUploadError !== UPLOAD_ERR_OK && $splitUploadError !== UPLOAD_ERR_NO_FILE) {
                    $error = upload_error_message($splitUploadError);
                }
            }

            if ($error === '' && isset($_FILES[$splitFileKey]) && is_array($_FILES[$splitFileKey]) && ($_FILES[$splitFileKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $tmpName = (string)$_FILES[$splitFileKey]['tmp_name'];
                $original = (string)$_FILES[$splitFileKey]['name'];
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($ext, $allowed, true)) {
                    $mime = detect_uploaded_mime_type($tmpName);
                    if (!in_array($mime, $allowedMime, true)) {
                        $error = 'Imagem lateral invalida. Envie JPG, PNG ou WEBP.';
                    } elseif (!is_uploaded_file($tmpName)) {
                        $error = 'Arquivo lateral de upload invalido.';
                    } elseif (!is_writable(UPLOADS_DIR)) {
                        $error = 'A pasta uploads/ nao tem permissao de escrita.';
                    } else {
                        $filename = 'split-' . $id . '-' . time() . '.' . $ext;
                        $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                        if (move_uploaded_file($tmpName, $destination)) {
                            $oldSplit = (string)($data['sections'][$index]['split_image'] ?? '');
                            if ($oldSplit !== '') {
                                $oldSplitPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldSplit);
                                if (is_file($oldSplitPath)) {
                                    @unlink($oldSplitPath);
                                }
                            }
                            $data['sections'][$index]['split_image'] = UPLOADS_URL . '/' . $filename;
                        } else {
                            $error = 'Falha ao salvar imagem lateral em uploads/.';
                        }
                    }
                } else {
                    $error = 'Formato invalido para imagem lateral. Use JPG, PNG ou WEBP.';
                }
            }

            $sectionVideoKey = 'section_video_' . $id;
            $sectionVideoBgKey = 'section_video_bg_' . $id;
            $activeSectionVideoKey = $sectionVideoKey;
            $sectionVideoUploadError = UPLOAD_ERR_NO_FILE;
            if ($error === '' && isset($_FILES[$sectionVideoKey]) && is_array($_FILES[$sectionVideoKey])) {
                $sectionVideoUploadError = (int)($_FILES[$sectionVideoKey]['error'] ?? UPLOAD_ERR_NO_FILE);
            }
            if ($error === '' && $sectionVideoUploadError === UPLOAD_ERR_NO_FILE && isset($_FILES[$sectionVideoBgKey]) && is_array($_FILES[$sectionVideoBgKey])) {
                $sectionVideoUploadError = (int)($_FILES[$sectionVideoBgKey]['error'] ?? UPLOAD_ERR_NO_FILE);
                $activeSectionVideoKey = $sectionVideoBgKey;
            }
            if ($error === '' && $sectionVideoUploadError !== UPLOAD_ERR_OK && $sectionVideoUploadError !== UPLOAD_ERR_NO_FILE) {
                $error = upload_error_message($sectionVideoUploadError);
            }

            if ($error === '' && $sectionVideoUploadError === UPLOAD_ERR_OK && isset($_FILES[$activeSectionVideoKey]) && is_array($_FILES[$activeSectionVideoKey])) {
                $tmpName = (string)$_FILES[$activeSectionVideoKey]['tmp_name'];
                $original = (string)$_FILES[$activeSectionVideoKey]['name'];
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                $allowed = ['mp4', 'webm', 'ogg'];
                $allowedMime = ['video/mp4', 'video/webm', 'video/ogg', 'video/x-m4v', 'application/mp4', 'application/octet-stream'];
                if (in_array($ext, $allowed, true)) {
                    $mime = detect_uploaded_mime_type($tmpName);
                    if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                        $error = 'Video da aba invalido. Use MP4, WEBM ou OGG.';
                    } elseif (!is_uploaded_file($tmpName)) {
                        $error = 'Arquivo de video da aba invalido.';
                    } elseif (!is_writable(UPLOADS_DIR)) {
                        $error = 'A pasta uploads/ nao tem permissao de escrita.';
                    } else {
                        $filename = 'secao-video-' . $id . '-' . time() . '.' . $ext;
                        $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                        if (move_uploaded_file($tmpName, $destination)) {
                            $oldVideo = (string)($data['sections'][$index]['section_video'] ?? '');
                            if ($oldVideo !== '') {
                                $oldVideoPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldVideo);
                                if (is_file($oldVideoPath)) {
                                    @unlink($oldVideoPath);
                                }
                            }
                            $data['sections'][$index]['section_video'] = UPLOADS_URL . '/' . $filename;
                        } else {
                            $error = 'Falha ao salvar video da aba em uploads/.';
                        }
                    }
                } else {
                    $error = 'Formato invalido para video da aba. Use MP4, WEBM ou OGG.';
                }
            }

            $linkedImagesCurrent = $data['sections'][$index]['linked_images'] ?? [];
            if (!is_array($linkedImagesCurrent)) {
                $linkedImagesCurrent = [];
            }
            $linkedImagesById = [];
            $maxLinkedImageId = 0;
            foreach ($linkedImagesCurrent as $linkedImage) {
                if (!is_array($linkedImage)) {
                    continue;
                }
                $linkedId = (int)($linkedImage['id'] ?? 0);
                if ($linkedId <= 0) {
                    $linkedId = $maxLinkedImageId + 1;
                }
                $linkedPath = sanitize_text((string)($linkedImage['image'] ?? ''));
                if ($linkedPath === '') {
                    continue;
                }
                $linkedImagesById[$linkedId] = [
                    'id' => $linkedId,
                    'image' => $linkedPath,
                    'link' => sanitize_text((string)($linkedImage['link'] ?? '')),
                    'alt' => sanitize_text((string)($linkedImage['alt'] ?? '')),
                    'order' => (int)($linkedImage['order'] ?? ($maxLinkedImageId + 1)),
                ];
                if ($linkedId > $maxLinkedImageId) {
                    $maxLinkedImageId = $linkedId;
                }
            }

            $postedLinkedIds = isset($_POST['linked_image_ids']) && is_array($_POST['linked_image_ids']) ? $_POST['linked_image_ids'] : [];
            $postedLinkedLinks = isset($_POST['linked_image_links']) && is_array($_POST['linked_image_links']) ? $_POST['linked_image_links'] : [];
            $postedLinkedAlts = isset($_POST['linked_image_alts']) && is_array($_POST['linked_image_alts']) ? $_POST['linked_image_alts'] : [];
            $postedLinkedRemoveIds = isset($_POST['linked_image_remove_ids']) && is_array($_POST['linked_image_remove_ids']) ? $_POST['linked_image_remove_ids'] : [];

            $removeLinkedMap = [];
            foreach ($postedLinkedRemoveIds as $removeLinkedId) {
                $removeId = (int)$removeLinkedId;
                if ($removeId > 0) {
                    $removeLinkedMap[$removeId] = true;
                }
            }

            $updatedLinkedImages = [];
            foreach ($postedLinkedIds as $rowIndex => $postedLinkedId) {
                $linkedId = (int)$postedLinkedId;
                if ($linkedId <= 0 || !isset($linkedImagesById[$linkedId])) {
                    continue;
                }

                $linkedItem = $linkedImagesById[$linkedId];

                if (isset($removeLinkedMap[$linkedId])) {
                    $linkedLocalPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string)$linkedItem['image']);
                    if (is_file($linkedLocalPath)) {
                        @unlink($linkedLocalPath);
                    }
                    continue;
                }

                $linkedItem['link'] = sanitize_text((string)($postedLinkedLinks[$rowIndex] ?? ''));
                $linkedItem['alt'] = sanitize_text((string)($postedLinkedAlts[$rowIndex] ?? ''));

                if ($error === '' && isset($_FILES['linked_image_files']) && is_array($_FILES['linked_image_files'])) {
                    $rowUploadError = (int)($_FILES['linked_image_files']['error'][$rowIndex] ?? UPLOAD_ERR_NO_FILE);
                    if ($rowUploadError !== UPLOAD_ERR_OK && $rowUploadError !== UPLOAD_ERR_NO_FILE) {
                        $error = upload_error_message($rowUploadError);
                    }

                    if ($error === '' && $rowUploadError === UPLOAD_ERR_OK) {
                        $tmpName = (string)($_FILES['linked_image_files']['tmp_name'][$rowIndex] ?? '');
                        $original = (string)($_FILES['linked_image_files']['name'][$rowIndex] ?? '');
                        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

                        if (in_array($ext, $allowed, true)) {
                            $mime = detect_uploaded_mime_type($tmpName);
                            if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                                $error = 'Imagem da galeria invalida. Envie JPG, PNG ou WEBP.';
                            } elseif (!is_uploaded_file($tmpName)) {
                                $error = 'Arquivo de imagem da galeria invalido.';
                            } elseif (!is_writable(UPLOADS_DIR)) {
                                $error = 'A pasta uploads/ nao tem permissao de escrita.';
                            } else {
                                $filename = 'linked-' . $id . '-' . $linkedId . '-' . time() . '.' . $ext;
                                $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                                if (move_uploaded_file($tmpName, $destination)) {
                                    $oldLinkedPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string)$linkedItem['image']);
                                    if (is_file($oldLinkedPath)) {
                                        @unlink($oldLinkedPath);
                                    }
                                    $linkedItem['image'] = UPLOADS_URL . '/' . $filename;
                                } else {
                                    $error = 'Falha ao salvar imagem da galeria em uploads/.';
                                }
                            }
                        } else {
                            $error = 'Formato invalido para imagem da galeria. Use JPG, PNG ou WEBP.';
                        }
                    }
                }

                $updatedLinkedImages[] = $linkedItem;
            }

            $newLinkedLink = sanitize_text((string)($_POST['linked_image_new_link'] ?? ''));
            $newLinkedAlt = sanitize_text((string)($_POST['linked_image_new_alt'] ?? ''));
            $newLinkedHasText = $newLinkedLink !== '' || $newLinkedAlt !== '';
            $newLinkedFile = $_FILES['linked_image_new_file'] ?? null;
            if ($error === '' && is_array($newLinkedFile)) {
                $newLinkedUploadError = (int)($newLinkedFile['error'] ?? UPLOAD_ERR_NO_FILE);
                if ($newLinkedUploadError !== UPLOAD_ERR_OK && $newLinkedUploadError !== UPLOAD_ERR_NO_FILE) {
                    $error = upload_error_message($newLinkedUploadError);
                }

                if ($error === '' && $newLinkedUploadError === UPLOAD_ERR_OK) {
                    $linkedImagesLimit = normalize_linked_images_limit($data['sections'][$index]['linked_images_limit'] ?? 6);
                    if (count($updatedLinkedImages) >= $linkedImagesLimit) {
                        $error = 'Limite de imagens desta aba atingido (' . $linkedImagesLimit . '). Aumente o limite ou remova uma imagem antes de adicionar outra.';
                    }
                }

                if ($error === '' && $newLinkedUploadError === UPLOAD_ERR_OK) {
                    $tmpName = (string)$newLinkedFile['tmp_name'];
                    $original = (string)$newLinkedFile['name'];
                    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
                    if (in_array($ext, $allowed, true)) {
                        $mime = detect_uploaded_mime_type($tmpName);
                        if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                            $error = 'Nova imagem da galeria invalida. Envie JPG, PNG ou WEBP.';
                        } elseif (!is_uploaded_file($tmpName)) {
                            $error = 'Arquivo da nova imagem da galeria invalido.';
                        } elseif (!is_writable(UPLOADS_DIR)) {
                            $error = 'A pasta uploads/ nao tem permissao de escrita.';
                        } else {
                            $maxLinkedImageId++;
                            $filename = 'linked-' . $id . '-' . $maxLinkedImageId . '-' . time() . '.' . $ext;
                            $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                            if (move_uploaded_file($tmpName, $destination)) {
                                $updatedLinkedImages[] = [
                                    'id' => $maxLinkedImageId,
                                    'image' => UPLOADS_URL . '/' . $filename,
                                    'link' => $newLinkedLink,
                                    'alt' => $newLinkedAlt,
                                    'order' => count($updatedLinkedImages) + 1,
                                ];
                            } else {
                                $error = 'Falha ao salvar nova imagem da galeria em uploads/.';
                            }
                        }
                    } else {
                        $error = 'Formato invalido para nova imagem da galeria. Use JPG, PNG ou WEBP.';
                    }
                } elseif ($error === '' && $newLinkedHasText) {
                    $error = 'Para adicionar uma nova imagem com link, envie o arquivo da imagem.';
                }
            } elseif ($error === '' && $newLinkedHasText) {
                $error = 'Para adicionar uma nova imagem com link, envie o arquivo da imagem.';
            }

            foreach ($updatedLinkedImages as $updatedIndex => &$linkedImageItem) {
                $linkedImageItem['order'] = $updatedIndex + 1;
            }
            unset($linkedImageItem);
            $data['sections'][$index]['linked_images'] = $updatedLinkedImages;

            $cardsCurrent = $data['sections'][$index]['cards_items'] ?? [];
            if (!is_array($cardsCurrent)) {
                $cardsCurrent = [];
            }
            $cardsById = [];
            $maxCardId = 0;
            foreach ($cardsCurrent as $cardItem) {
                if (!is_array($cardItem)) {
                    continue;
                }
                $cardId = (int)($cardItem['id'] ?? 0);
                if ($cardId <= 0) {
                    $cardId = $maxCardId + 1;
                }
                $cardsById[$cardId] = [
                    'id' => $cardId,
                    'title' => sanitize_text((string)($cardItem['title'] ?? '')),
                    'text' => sanitize_text((string)($cardItem['text'] ?? '')),
                    'image' => sanitize_text((string)($cardItem['image'] ?? '')),
                    'button_text' => sanitize_text((string)($cardItem['button_text'] ?? '')),
                    'button_link' => sanitize_text((string)($cardItem['button_link'] ?? '')),
                    'order' => (int)($cardItem['order'] ?? ($maxCardId + 1)),
                ];
                if ($cardId > $maxCardId) {
                    $maxCardId = $cardId;
                }
            }

            $postedCardIds = isset($_POST['card_ids']) && is_array($_POST['card_ids']) ? $_POST['card_ids'] : [];
            $postedCardTitles = isset($_POST['card_titles']) && is_array($_POST['card_titles']) ? $_POST['card_titles'] : [];
            $postedCardTexts = isset($_POST['card_texts']) && is_array($_POST['card_texts']) ? $_POST['card_texts'] : [];
            $postedCardButtonTexts = isset($_POST['card_button_texts']) && is_array($_POST['card_button_texts']) ? $_POST['card_button_texts'] : [];
            $postedCardButtonLinks = isset($_POST['card_button_links']) && is_array($_POST['card_button_links']) ? $_POST['card_button_links'] : [];
            $postedCardRemoveIds = isset($_POST['card_remove_ids']) && is_array($_POST['card_remove_ids']) ? $_POST['card_remove_ids'] : [];
            $postedCardRemoveImageIds = isset($_POST['card_remove_image_ids']) && is_array($_POST['card_remove_image_ids']) ? $_POST['card_remove_image_ids'] : [];

            $removeCardMap = [];
            foreach ($postedCardRemoveIds as $removeCardId) {
                $removeId = (int)$removeCardId;
                if ($removeId > 0) {
                    $removeCardMap[$removeId] = true;
                }
            }
            $removeCardImageMap = [];
            foreach ($postedCardRemoveImageIds as $removeCardImageId) {
                $removeId = (int)$removeCardImageId;
                if ($removeId > 0) {
                    $removeCardImageMap[$removeId] = true;
                }
            }

            $updatedCards = [];
            foreach ($postedCardIds as $rowIndex => $postedCardId) {
                $cardId = (int)$postedCardId;
                if ($cardId <= 0 || !isset($cardsById[$cardId])) {
                    continue;
                }

                $cardItem = $cardsById[$cardId];
                if (isset($removeCardMap[$cardId])) {
                    $cardImagePath = (string)($cardItem['image'] ?? '');
                    if ($cardImagePath !== '') {
                        $localCardImagePath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cardImagePath);
                        if (is_file($localCardImagePath)) {
                            @unlink($localCardImagePath);
                        }
                    }
                    continue;
                }

                $cardItem['title'] = sanitize_text((string)($postedCardTitles[$rowIndex] ?? ''));
                $cardItem['text'] = sanitize_text((string)($postedCardTexts[$rowIndex] ?? ''));
                $cardItem['button_text'] = sanitize_text((string)($postedCardButtonTexts[$rowIndex] ?? ''));
                $cardItem['button_link'] = sanitize_text((string)($postedCardButtonLinks[$rowIndex] ?? ''));

                if (isset($removeCardImageMap[$cardId]) && (string)$cardItem['image'] !== '') {
                    $oldCardImagePath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string)$cardItem['image']);
                    if (is_file($oldCardImagePath)) {
                        @unlink($oldCardImagePath);
                    }
                    $cardItem['image'] = '';
                }

                if ($error === '' && isset($_FILES['card_image_files']) && is_array($_FILES['card_image_files'])) {
                    $rowUploadError = (int)($_FILES['card_image_files']['error'][$rowIndex] ?? UPLOAD_ERR_NO_FILE);
                    if ($rowUploadError !== UPLOAD_ERR_OK && $rowUploadError !== UPLOAD_ERR_NO_FILE) {
                        $error = upload_error_message($rowUploadError);
                    }

                    if ($error === '' && $rowUploadError === UPLOAD_ERR_OK) {
                        $tmpName = (string)($_FILES['card_image_files']['tmp_name'][$rowIndex] ?? '');
                        $original = (string)($_FILES['card_image_files']['name'][$rowIndex] ?? '');
                        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
                        if (in_array($ext, $allowed, true)) {
                            $mime = detect_uploaded_mime_type($tmpName);
                            if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                                $error = 'Imagem do card invalida. Envie JPG, PNG ou WEBP.';
                            } elseif (!is_uploaded_file($tmpName)) {
                                $error = 'Arquivo de imagem do card invalido.';
                            } elseif (!is_writable(UPLOADS_DIR)) {
                                $error = 'A pasta uploads/ nao tem permissao de escrita.';
                            } else {
                                $filename = 'card-' . $id . '-' . $cardId . '-' . time() . '.' . $ext;
                                $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                                if (move_uploaded_file($tmpName, $destination)) {
                                    $oldCardImagePath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string)$cardItem['image']);
                                    if ((string)$cardItem['image'] !== '' && is_file($oldCardImagePath)) {
                                        @unlink($oldCardImagePath);
                                    }
                                    $cardItem['image'] = UPLOADS_URL . '/' . $filename;
                                } else {
                                    $error = 'Falha ao salvar imagem do card em uploads/.';
                                }
                            }
                        } else {
                            $error = 'Formato invalido para imagem do card. Use JPG, PNG ou WEBP.';
                        }
                    }
                }

                if ($cardItem['title'] === '' && $cardItem['text'] === '' && (string)$cardItem['image'] === '') {
                    continue;
                }
                $updatedCards[] = $cardItem;
                if (count($updatedCards) >= 6) {
                    break;
                }
            }

            $newCardTitle = sanitize_text((string)($_POST['card_new_title'] ?? ''));
            $newCardText = sanitize_text((string)($_POST['card_new_text'] ?? ''));
            $newCardButtonText = sanitize_text((string)($_POST['card_new_button_text'] ?? ''));
            $newCardButtonLink = sanitize_text((string)($_POST['card_new_button_link'] ?? ''));
            $newCardFile = $_FILES['card_new_file'] ?? null;
            $newCardHasFile = is_array($newCardFile) && (int)($newCardFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
            $newCardHasAnyInput = $newCardTitle !== '' || $newCardText !== '' || $newCardButtonText !== '' || $newCardButtonLink !== '' || $newCardHasFile;

            if ($error === '' && $newCardHasAnyInput) {
                if (count($updatedCards) >= 6) {
                    $error = 'Limite de cards desta aba atingido (6). Remova um card antes de adicionar outro.';
                }
            }

            if ($error === '' && is_array($newCardFile)) {
                $newCardUploadError = (int)($newCardFile['error'] ?? UPLOAD_ERR_NO_FILE);
                if ($newCardUploadError !== UPLOAD_ERR_OK && $newCardUploadError !== UPLOAD_ERR_NO_FILE) {
                    $error = upload_error_message($newCardUploadError);
                }
            }

            $newCardImagePath = '';
            if ($error === '' && is_array($newCardFile) && (int)($newCardFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $tmpName = (string)$newCardFile['tmp_name'];
                $original = (string)$newCardFile['name'];
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($ext, $allowed, true)) {
                    $mime = detect_uploaded_mime_type($tmpName);
                    if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
                        $error = 'Nova imagem do card invalida. Envie JPG, PNG ou WEBP.';
                    } elseif (!is_uploaded_file($tmpName)) {
                        $error = 'Arquivo da nova imagem do card invalido.';
                    } elseif (!is_writable(UPLOADS_DIR)) {
                        $error = 'A pasta uploads/ nao tem permissao de escrita.';
                    } else {
                        $maxCardId++;
                        $filename = 'card-' . $id . '-' . $maxCardId . '-' . time() . '.' . $ext;
                        $destination = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;
                        if (move_uploaded_file($tmpName, $destination)) {
                            $newCardImagePath = UPLOADS_URL . '/' . $filename;
                        } else {
                            $error = 'Falha ao salvar nova imagem do card em uploads/.';
                        }
                    }
                } else {
                    $error = 'Formato invalido para nova imagem do card. Use JPG, PNG ou WEBP.';
                }
            }

            if ($error === '' && $newCardHasAnyInput) {
                if ($newCardTitle === '' && $newCardText === '' && $newCardImagePath === '') {
                    $error = 'Para adicionar um card, preencha titulo, descricao ou envie uma imagem.';
                } else {
                    if ($newCardImagePath === '') {
                        $maxCardId++;
                    }
                    $updatedCards[] = [
                        'id' => $maxCardId,
                        'title' => $newCardTitle,
                        'text' => $newCardText,
                        'image' => $newCardImagePath,
                        'button_text' => $newCardButtonText,
                        'button_link' => $newCardButtonLink,
                        'order' => count($updatedCards) + 1,
                    ];
                }
            }

            foreach ($updatedCards as $updatedIndex => &$cardItem) {
                $cardItem['order'] = $updatedIndex + 1;
            }
            unset($cardItem);
            $data['sections'][$index]['cards_items'] = array_slice($updatedCards, 0, 6);

            save_site_data($data);
            if ($error === '') {
                $message = 'Aba atualizada com sucesso.';
            }
        }
    }

    if ($action === 'delete_section') {
        $id = (int)($_POST['id'] ?? 0);
        $index = find_section_index($data['sections'], $id);

        if ($index !== null) {
            $image = (string)($data['sections'][$index]['background_image'] ?? '');
            $splitImage = (string)($data['sections'][$index]['split_image'] ?? '');
            $sectionVideo = (string)($data['sections'][$index]['section_video'] ?? '');
            $linkedImages = $data['sections'][$index]['linked_images'] ?? [];
            $cardsItems = $data['sections'][$index]['cards_items'] ?? [];
            if ($image !== '') {
                $imgPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image);
                if (is_file($imgPath)) {
                    @unlink($imgPath);
                }
            }
            if ($splitImage !== '') {
                $splitPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $splitImage);
                if (is_file($splitPath)) {
                    @unlink($splitPath);
                }
            }
            if ($sectionVideo !== '') {
                $videoPath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sectionVideo);
                if (is_file($videoPath)) {
                    @unlink($videoPath);
                }
            }
            if (is_array($linkedImages)) {
                foreach ($linkedImages as $linkedImage) {
                    if (!is_array($linkedImage)) {
                        continue;
                    }
                    $linkedPath = sanitize_text((string)($linkedImage['image'] ?? ''));
                    if ($linkedPath === '') {
                        continue;
                    }
                    $linkedFilePath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $linkedPath);
                    if (is_file($linkedFilePath)) {
                        @unlink($linkedFilePath);
                    }
                }
            }
            if (is_array($cardsItems)) {
                foreach ($cardsItems as $cardItem) {
                    if (!is_array($cardItem)) {
                        continue;
                    }
                    $cardImage = sanitize_text((string)($cardItem['image'] ?? ''));
                    if ($cardImage === '') {
                        continue;
                    }
                    $cardImagePath = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cardImage);
                    if (is_file($cardImagePath)) {
                        @unlink($cardImagePath);
                    }
                }
            }

            array_splice($data['sections'], $index, 1);
            foreach ($data['sections'] as $i => &$section) {
                $section['order'] = $i + 1;
            }
            unset($section);

            save_site_data($data);
            $message = 'Aba removida.';
        }
    }

    if ($action === 'move_section') {
        $id = (int)($_POST['id'] ?? 0);
        $direction = (string)($_POST['direction'] ?? 'up');
        usort($data['sections'], static fn(array $a, array $b): int => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        $current = find_section_index($data['sections'], $id);
        if ($current !== null) {
            $target = $direction === 'up' ? $current - 1 : $current + 1;
            if (isset($data['sections'][$target])) {
                $tmp = $data['sections'][$target];
                $data['sections'][$target] = $data['sections'][$current];
                $data['sections'][$current] = $tmp;

                foreach ($data['sections'] as $i => &$section) {
                    $section['order'] = $i + 1;
                }
                unset($section);

                save_site_data($data);
                $message = 'Ordem das abas atualizada.';
            }
        }
    }

    if ($action === 'update_admin') {
        $newUser = sanitize_text((string)($_POST['admin_user'] ?? 'admin'));
        $newPass = (string)($_POST['admin_pass'] ?? '');

        $data['admin']['username'] = $newUser !== '' ? $newUser : 'admin';

        if ($newPass !== '') {
            $data['admin']['password_hash'] = password_hash($newPass, PASSWORD_DEFAULT);
            $data['admin']['password_plain'] = '';
            $message = 'Usuario e senha do dashboard atualizados.';
        } else {
            $message = 'Usuario do dashboard atualizado.';
        }

        save_site_data($data);
    }

    $redirectParams = [
        'panel' => $returnPanel,
        'status' => $error === '' ? 'success' : 'error',
        'message' => $error === '' ? ($message !== '' ? $message : 'Alteracoes salvas com sucesso.') : $error,
    ];
    if ($returnPanel === 'secoes' && $returnSectionId > 0) {
        $redirectParams['section'] = $returnSectionId;
    }
    header('Location: index.php?' . http_build_query($redirectParams));
    exit;
}

$meta = $data['meta'];
$sections = $data['sections'];
$fontOptions = available_font_options();
$panelLabels = [
    'geral' => 'Dados gerais',
    'criar' => 'Nova secao',
    'secoes' => 'Gerenciar secoes',
    'acesso' => 'Acesso e seguranca',
];
$activePanel = sanitize_text((string)($_POST['return_panel'] ?? $_GET['panel'] ?? 'geral'));
if (!array_key_exists($activePanel, $panelLabels)) {
    $activePanel = 'geral';
}
$activeSectionId = (int)($_POST['return_section_id'] ?? $_GET['section'] ?? 0);
if ($activePanel !== 'secoes') {
    $activeSectionId = 0;
}
$uploadMaxFilesize = (string)ini_get('upload_max_filesize');
$postMaxSize = (string)ini_get('post_max_size');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel de Controle</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<header class="admin-header">
    <h1>Painel de Controle</h1>
    <div class="admin-actions">
        <button type="button" class="outline sidebar-toggle" id="sidebarToggle">Menu</button>
        <a class="outline" href="../index.php" target="_blank" rel="noopener">Ver site</a>
        <a class="danger" href="logout.php">Sair</a>
    </div>
</header>

<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand"><?= e($meta['site_name'] ?? 'Minha Empresa') ?></div>
        <nav class="sidebar-nav">
            <button type="button" class="sidebar-link <?= $activePanel === 'geral' ? 'is-active' : '' ?>" data-target="geral" data-label="Dados gerais">
                <span class="nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 3l9 8h-3v9h-5v-6H11v6H6v-9H3l9-8z"/></svg>
                </span>
                <span>Dados gerais</span>
            </button>
            <button type="button" class="sidebar-link <?= $activePanel === 'criar' ? 'is-active' : '' ?>" data-target="criar" data-label="Nova secao">
                <span class="nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M11 3h2v8h8v2h-8v8h-2v-8H3v-2h8V3z"/></svg>
                </span>
                <span>Nova secao</span>
            </button>
            <button type="button" class="sidebar-link <?= $activePanel === 'secoes' ? 'is-active' : '' ?>" data-target="secoes" data-label="Gerenciar secoes">
                <span class="nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h10v2H4v-2z"/></svg>
                </span>
                <span>Gerenciar secoes</span>
            </button>
            <div class="sidebar-submenu" data-parent="secoes">
                <?php foreach ($sections as $sidebarSection): ?>
                    <button
                        type="button"
                        class="sidebar-sub-link <?= $activePanel === 'secoes' && $activeSectionId === (int)($sidebarSection['id'] ?? 0) ? 'is-active' : '' ?>"
                        data-target="secoes"
                        data-section-id="<?= (int)($sidebarSection['id'] ?? 0) ?>"
                        title="<?= e($sidebarSection['title'] ?? 'Secao') ?>"
                    >
                        <?= e($sidebarSection['title'] ?? 'Secao') ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <button type="button" class="sidebar-link <?= $activePanel === 'acesso' ? 'is-active' : '' ?>" data-target="acesso" data-label="Acesso e seguranca">
                <span class="nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 2l8 4v6c0 5-3.4 9.7-8 11-4.6-1.3-8-6-8-11V6l8-4zm0 8a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </span>
                <span>Acesso e seguranca</span>
            </button>
        </nav>
        <div class="sidebar-user">
            <div class="sidebar-avatar">A</div>
            <div class="sidebar-user-name"><?= e($data['admin']['username'] ?? 'admin') ?></div>
        </div>
    </aside>

<main class="container admin-panels">
    <div class="dashboard-breadcrumb" id="dashboardBreadcrumb">
        <span class="crumb-home">Painel</span>
        <span class="crumb-sep">/</span>
        <strong class="crumb-current"><?= e($panelLabels[$activePanel] ?? 'Dados gerais') ?></strong>
    </div>
    <?php if ($message !== ''): ?><div class="success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <?php
    $heroModeValue = (string)($meta['hero_mode'] ?? 'text');
    if ($heroModeValue === 'video') {
        $heroModeValue = 'video_full';
    }
    if (!in_array($heroModeValue, ['text', 'video_full', 'video_background', 'video_split', 'image_full', 'image_background', 'image_split'], true)) {
        $heroModeValue = 'text';
    }
    $heroFunctionValue = 'text';
    $heroVideoVariantValue = 'background_text';
    $heroImageVariantValue = 'background_text';
    if (in_array($heroModeValue, ['video_full', 'video_background', 'video_split'], true)) {
        $heroFunctionValue = 'video';
        $heroVideoVariantValue = $heroModeValue === 'video_full' ? 'full' : ($heroModeValue === 'video_split' ? 'split_text' : 'background_text');
    } elseif (in_array($heroModeValue, ['image_full', 'image_background', 'image_split'], true)) {
        $heroFunctionValue = 'image';
        $heroImageVariantValue = $heroModeValue === 'image_full' ? 'full' : ($heroModeValue === 'image_split' ? 'split_text' : 'background_text');
    }
    ?>

    <section class="card panel-card <?= $activePanel === 'geral' ? 'is-active' : '' ?>" data-panel="geral">
        <div class="panel-head">
            <h2>Dados gerais</h2>
            <button type="button" class="collapse-toggle" data-label-open="Minimizar" data-label-closed="Expandir">Minimizar</button>
        </div>
        <div class="panel-body">
        <form method="post" enctype="multipart/form-data" class="grid-2 section-form">
            <input type="hidden" name="action" value="update_meta">
            <input type="hidden" name="return_panel" value="geral">
            <div class="full feature-config-group">
                <div class="section-config-title">Menu de cima</div>
                <div class="feature-grid">
                    <label>Nome da empresa
                        <input type="text" name="site_name" value="<?= e($meta['site_name'] ?? '') ?>" required>
                    </label>
                    <label>Fonte do nome da empresa
                        <select name="brand_font">
                            <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                <option value="<?= e($fontKey) ?>" <?= ($meta['brand_font'] ?? 'sora') === $fontKey ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Tamanho do nome da empresa (px)
                        <input type="number" name="brand_size" min="12" max="252" value="<?= (int)($meta['brand_size'] ?? 18) ?>">
                    </label>
                    <label class="full">Logo da empresa (JPG, PNG, WEBP, SVG)
                        <input type="file" name="logo_image" accept=".jpg,.jpeg,.png,.webp,.svg">
                    </label>
                    <?php if (!empty($meta['logo_image'])): ?>
                        <div class="full preview-wrap">
                            <img src="../<?= e($meta['logo_image']) ?>" alt="Preview da logo" class="preview preview-logo">
                        </div>
                    <?php endif; ?>
                    <label class="inline-check full">
                        <input type="checkbox" name="remove_logo" value="1">
                        <span>Remover logo atual</span>
                    </label>
                    <label class="full">Texto alternativo da logo
                        <input type="text" name="logo_alt" value="<?= e($meta['logo_alt'] ?? 'Logo da empresa') ?>">
                    </label>
                </div>
            </div>

            <div class="full feature-config-group">
                <div class="section-config-title">Aba principal (topo)</div>
                <label class="full">Funcao da aba principal de inicio
                    <select name="hero_function" class="hero-function-select">
                        <option value="text" <?= $heroFunctionValue === 'text' ? 'selected' : '' ?>>1. Primario + secundario + textos</option>
                        <option value="video" <?= $heroFunctionValue === 'video' ? 'selected' : '' ?>>2. Video (com texto ou so video)</option>
                        <option value="image" <?= $heroFunctionValue === 'image' ? 'selected' : '' ?>>3. Imagem (igual ao item de video)</option>
                    </select>
                    <small class="size-preview">Escolha a funcao e ajuste as opcoes da caixa abaixo.</small>
                </label>
                <div class="mode-config-group" data-hero-group="video_variant">
                    <div class="section-config-title">Configuracao do modo Video</div>
                    <div class="feature-grid">
                        <label>Modo de exibicao do video
                            <select name="hero_video_variant">
                                <option value="background_text" <?= $heroVideoVariantValue === 'background_text' ? 'selected' : '' ?>>Video com texto no fundo</option>
                                <option value="split_text" <?= $heroVideoVariantValue === 'split_text' ? 'selected' : '' ?>>Video com texto na lateral</option>
                                <option value="full" <?= $heroVideoVariantValue === 'full' ? 'selected' : '' ?>>So video (tela inteira)</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="mode-config-group" data-hero-group="image_variant">
                    <div class="section-config-title">Configuracao do modo Imagem</div>
                    <div class="feature-grid">
                        <label>Modo de exibicao da imagem
                            <select name="hero_image_variant">
                                <option value="background_text" <?= $heroImageVariantValue === 'background_text' ? 'selected' : '' ?>>Imagem com texto no fundo</option>
                                <option value="split_text" <?= $heroImageVariantValue === 'split_text' ? 'selected' : '' ?>>Imagem com texto na lateral</option>
                                <option value="full" <?= $heroImageVariantValue === 'full' ? 'selected' : '' ?>>So imagem (tela inteira)</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="mode-config-group" data-hero-group="text_content">
                    <div class="section-config-title">Conteudo textual da aba principal</div>
                    <div class="feature-grid">
                        <label>Titulo principal
                            <input type="text" name="headline" value="<?= e($meta['headline'] ?? '') ?>" required>
                        </label>
                        <label>Cor do texto principal
                            <input type="color" name="hero_text_color" value="<?= e($meta['hero_text_color'] ?? '#f6f2eb') ?>">
                        </label>
                        <label>Fonte do titulo principal
                            <select name="headline_font">
                                <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                    <option value="<?= e($fontKey) ?>" <?= ($meta['headline_font'] ?? 'sora') === $fontKey ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>Tamanho do titulo principal (px)
                            <input type="number" name="headline_size" min="16" max="252" value="<?= (int)min((int)($meta['headline_size'] ?? 42), 252) ?>">
                        </label>
                        <label class="full">Texto de abertura
                            <textarea name="intro" rows="3"><?= e($meta['intro'] ?? '') ?></textarea>
                        </label>
                        <label>Fonte do texto de abertura
                            <select name="intro_font">
                                <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                    <option value="<?= e($fontKey) ?>" <?= ($meta['intro_font'] ?? 'manrope') === $fontKey ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>Tamanho do texto de abertura (px)
                            <input type="number" name="intro_size" min="12" max="252" value="<?= (int)($meta['intro_size'] ?? 17) ?>">
                        </label>
                        <label>Cor primaria da aba principal
                            <input type="color" name="primary_color" value="<?= e($meta['primary_color'] ?? '#113946') ?>">
                        </label>
                        <label>Cor secundaria da aba principal
                            <input type="color" name="secondary_color" value="<?= e($meta['secondary_color'] ?? '#bca37f') ?>">
                        </label>
                    </div>
                </div>
                <div class="mode-config-group" data-hero-group="split_layout">
                    <div class="section-config-title">Configuracao de midia lateral</div>
                    <div class="feature-grid">
                        <label>Posicao da midia lateral (pagina principal)
                            <select name="hero_layout">
                                <option value="split-left" <?= ($meta['hero_layout'] ?? 'split-right') === 'split-left' ? 'selected' : '' ?>>Midia esquerda / texto direita</option>
                                <option value="split-right" <?= ($meta['hero_layout'] ?? 'split-right') === 'split-right' ? 'selected' : '' ?>>Texto esquerda / midia direita</option>
                            </select>
                        </label>
                        <label>Tamanho da midia lateral (pagina principal)
                            <select name="hero_split_size">
                                <option value="small" <?= ($meta['hero_split_size'] ?? '') === 'small' ? 'selected' : '' ?>>Pequeno</option>
                                <option value="medium" <?= ($meta['hero_split_size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medio</option>
                                <option value="large" <?= ($meta['hero_split_size'] ?? '') === 'large' ? 'selected' : '' ?>>Grande</option>
                            </select>
                        </label>
                        <label>Largura customizada da midia (px)
                            <input type="number" name="hero_split_width" min="0" max="2000" value="<?= (int)($meta['hero_split_width'] ?? 0) ?>">
                            <small class="size-preview">0 = usar Pequeno/Medio/Grande</small>
                        </label>
                        <label>Altura customizada da midia (px)
                            <input type="number" name="hero_split_height" min="0" max="2000" value="<?= (int)($meta['hero_split_height'] ?? 0) ?>">
                            <small class="size-preview">0 = altura automatica</small>
                        </label>
                        <label>Encaixe da midia lateral (pagina principal)
                            <select name="hero_split_fit">
                                <option value="cover" <?= ($meta['hero_split_fit'] ?? 'cover') === 'cover' ? 'selected' : '' ?>>Preencher area (corta bordas)</option>
                                <option value="contain" <?= ($meta['hero_split_fit'] ?? '') === 'contain' ? 'selected' : '' ?>>Mostrar midia inteira</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="mode-config-group" data-hero-group="video_file">
                    <div class="section-config-title">Arquivo de video da aba principal</div>
                    <div class="feature-grid">
                        <label>Video da pagina principal (MP4, WEBM, OGG)
                            <input type="file" name="hero_video" accept=".mp4,.webm,.ogg">
                        </label>
                        <label class="inline-check">
                            <input type="checkbox" name="remove_hero_video" value="1">
                            <span>Remover video principal</span>
                        </label>
                    </div>
                    <?php if (!empty($meta['hero_video'])): ?>
                        <div class="full preview-wrap">
                            <video controls class="preview" src="../<?= e($meta['hero_video']) ?>"></video>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mode-config-group" data-hero-group="image_file">
                    <div class="section-config-title">Arquivo de imagem da aba principal</div>
                    <div class="feature-grid">
                        <label>Imagem da pagina principal (JPG, PNG, WEBP)
                            <input type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp">
                        </label>
                        <label class="inline-check">
                            <input type="checkbox" name="remove_hero_image" value="1">
                            <span>Remover imagem principal</span>
                        </label>
                    </div>
                    <?php if (!empty($meta['hero_image'])): ?>
                        <div class="full preview-wrap">
                            <img src="../<?= e($meta['hero_image']) ?>" alt="Imagem da aba principal" class="preview linked-preview">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mode-config-group" data-hero-group="text_only_note">
                    <p class="muted-note">No modo texto, a aba principal mostra apenas titulo e texto com as cores primaria e secundaria.</p>
                </div>
                <div class="mode-config-group" data-hero-group="media_only_note">
                    <p class="muted-note">No modo "so midia", os textos da aba principal ficam ocultos no site.</p>
                </div>
                <div class="mode-config-group" data-hero-group="extras">
                    <div class="section-config-title">Funcoes extras da aba principal</div>
                    <div class="feature-grid">
                        <label>Animacao de entrada das secoes
                            <select name="animation_style">
                                <option value="slide" <?= ($meta['animation_style'] ?? 'slide') === 'slide' ? 'selected' : '' ?>>Slide suave</option>
                                <option value="fade" <?= ($meta['animation_style'] ?? '') === 'fade' ? 'selected' : '' ?>>Fade</option>
                                <option value="zoom" <?= ($meta['animation_style'] ?? '') === 'zoom' ? 'selected' : '' ?>>Zoom in</option>
                                <option value="lift" <?= ($meta['animation_style'] ?? '') === 'lift' ? 'selected' : '' ?>>Lift (subida forte)</option>
                                <option value="left" <?= ($meta['animation_style'] ?? '') === 'left' ? 'selected' : '' ?>>Vem da esquerda</option>
                                <option value="right" <?= ($meta['animation_style'] ?? '') === 'right' ? 'selected' : '' ?>>Vem da direita</option>
                                <option value="top" <?= ($meta['animation_style'] ?? '') === 'top' ? 'selected' : '' ?>>Vem de cima</option>
                                <option value="letters" <?= ($meta['animation_style'] ?? '') === 'letters' ? 'selected' : '' ?>>Animacao de letras</option>
                                <option value="none" <?= ($meta['animation_style'] ?? '') === 'none' ? 'selected' : '' ?>>Sem animacao</option>
                            </select>
                        </label>
                        <label>Movimento do hero (topo)
                            <select name="hero_motion">
                                <option value="soft" <?= ($meta['hero_motion'] ?? 'soft') === 'soft' ? 'selected' : '' ?>>Suave</option>
                                <option value="dramatic" <?= ($meta['hero_motion'] ?? '') === 'dramatic' ? 'selected' : '' ?>>Dramatico</option>
                                <option value="none" <?= ($meta['hero_motion'] ?? '') === 'none' ? 'selected' : '' ?>>Sem movimento</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>

            <div class="full feature-config-group">
                <div class="section-config-title">Rodape</div>
                <div class="feature-grid">
                    <label>Telefone
                        <input type="text" name="contact_phone" value="<?= e($meta['contact_phone'] ?? '') ?>">
                    </label>
                    <label>E-mail
                        <input type="email" name="contact_email" value="<?= e($meta['contact_email'] ?? '') ?>">
                    </label>
                    <label class="full">Endereco
                        <input type="text" name="address" value="<?= e($meta['address'] ?? '') ?>">
                    </label>
                    <label>WhatsApp (com DDI)
                        <input type="text" name="whatsapp" value="<?= e($meta['whatsapp'] ?? '') ?>" placeholder="5511999999999">
                    </label>
                    <label>Fonte dos contatos do rodape
                        <select name="footer_font">
                            <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                <option value="<?= e($fontKey) ?>" <?= ($meta['footer_font'] ?? 'manrope') === $fontKey ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Tamanho dos contatos do rodape (px)
                        <input type="number" name="footer_size" min="12" max="252" value="<?= (int)($meta['footer_size'] ?? 16) ?>">
                    </label>
                </div>
                <div class="section-config-title">Redes sociais do rodape</div>
                <div class="feature-grid">
                    <label class="inline-check">
                        <input type="checkbox" name="social_facebook_enabled" value="1" <?= !empty($meta['social_facebook_enabled']) ? 'checked' : '' ?>>
                        <span>Ativar Facebook</span>
                    </label>
                    <label>Link Facebook
                        <input type="url" name="social_facebook_url" value="<?= e($meta['social_facebook_url'] ?? '') ?>" placeholder="https://facebook.com/suaempresa">
                    </label>
                    <label class="inline-check">
                        <input type="checkbox" name="social_instagram_enabled" value="1" <?= !empty($meta['social_instagram_enabled']) ? 'checked' : '' ?>>
                        <span>Ativar Instagram</span>
                    </label>
                    <label>Link Instagram
                        <input type="url" name="social_instagram_url" value="<?= e($meta['social_instagram_url'] ?? '') ?>" placeholder="https://instagram.com/suaempresa">
                    </label>
                    <label class="inline-check">
                        <input type="checkbox" name="social_linkedin_enabled" value="1" <?= !empty($meta['social_linkedin_enabled']) ? 'checked' : '' ?>>
                        <span>Ativar LinkedIn</span>
                    </label>
                    <label>Link LinkedIn
                        <input type="url" name="social_linkedin_url" value="<?= e($meta['social_linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/company/suaempresa">
                    </label>
                    <label class="inline-check">
                        <input type="checkbox" name="social_behance_enabled" value="1" <?= !empty($meta['social_behance_enabled']) ? 'checked' : '' ?>>
                        <span>Ativar Behance</span>
                    </label>
                    <label>Link Behance
                        <input type="url" name="social_behance_url" value="<?= e($meta['social_behance_url'] ?? '') ?>" placeholder="https://behance.net/suaempresa">
                    </label>
                    <label class="inline-check">
                        <input type="checkbox" name="social_youtube_enabled" value="1" <?= !empty($meta['social_youtube_enabled']) ? 'checked' : '' ?>>
                        <span>Ativar YouTube</span>
                    </label>
                    <label>Link YouTube
                        <input type="url" name="social_youtube_url" value="<?= e($meta['social_youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@suaempresa">
                    </label>
                </div>
            </div>
            <div class="full">
                <button type="submit">Salvar dados gerais</button>
            </div>
        </form>
        </div>
    </section>

    <section class="card panel-card <?= $activePanel === 'criar' ? 'is-active' : '' ?>" data-panel="criar">
        <div class="panel-head">
            <h2>Criar nova aba</h2>
            <button type="button" class="collapse-toggle" data-label-open="Minimizar" data-label-closed="Expandir">Minimizar</button>
        </div>
        <div class="panel-body">
        <form method="post" class="grid-2 section-form">
            <input type="hidden" name="action" value="add_section">
            <input type="hidden" name="return_panel" value="criar">
            <div class="full section-config-title">Dados basicos da aba</div>
            <label>Titulo da aba
                <input type="text" name="title" required>
            </label>
            <label>Nome no menu
                <input type="text" name="menu_label" required>
            </label>
            <label>Slug da URL ancora (ex: servicos-premium)
                <input type="text" name="slug" required>
            </label>
            <label>Cor de fundo padrao
                <input type="color" name="background_color" value="#ffffff">
            </label>
            <label>Animacao desta aba
                <select name="section_animation">
                    <option value="inherit">Herdar do geral</option>
                    <option value="slide">Slide suave</option>
                    <option value="fade">Fade</option>
                    <option value="zoom">Zoom in</option>
                    <option value="lift">Lift</option>
                    <option value="left">Vem da esquerda</option>
                    <option value="right">Vem da direita</option>
                    <option value="top">Vem de cima</option>
                    <option value="letters">Animacao de letras</option>
                    <option value="none">Sem animacao</option>
                </select>
            </label>
            <div class="full section-config-title">Funcoes da aba (principal + extra)</div>
            <label class="full">Selecione a funcao desta aba
                <select name="section_function" class="section-function-select">
                    <option value="basic_text" selected>1. Titulo e texto (fundo por cor)</option>
                    <option value="split_media">2. Titulo + texto com imagem/video lateral</option>
                    <option value="background_media">3. Titulo + texto com imagem/video no fundo</option>
                    <option value="cards_media">4. Cards com texto e imagens</option>
                    <option value="cards_text">5. Cards com titulos e textos</option>
                    <option value="linked_gallery">6. Imagens com links</option>
                    <option value="youtube">7. Video do YouTube</option>
                    <option value="map">8. Mapa do Google Maps</option>
                    <option value="contact_form">9. Formulario de contato</option>
                </select>
                <small class="size-preview">Ao escolher a funcao, a caixa de configuracao correspondente aparece abaixo.</small>
            </label>
            <div class="full mode-config-group" data-section-function="basic_text">
                <div class="section-config-title">1. Titulo e texto com configuracao da cor de fundo</div>
                <p class="muted-note">A cor de fundo fica no bloco "Dados basicos da aba".</p>
            </div>
            <div class="full mode-config-group" data-section-function="split_media">
                <div class="section-config-title">2. Titulo + texto com imagem/video lateral</div>
                <div class="feature-grid">
                    <label>Midia lateral
                        <select name="split_media_type">
                            <option value="image" selected>Imagem lateral</option>
                            <option value="video">Video lateral</option>
                        </select>
                    </label>
                    <label>Layout da aba
                        <select name="layout_mode">
                            <option value="split-left">Imagem/video esquerda / texto direita</option>
                            <option value="split-right" selected>Texto esquerda / imagem/video direita</option>
                        </select>
                    </label>
                    <label>Tamanho da imagem/video lateral
                        <select name="split_size">
                            <option value="small">Pequeno</option>
                            <option value="medium" selected>Medio</option>
                            <option value="large">Grande</option>
                        </select>
                        <small class="size-preview">Largura estimada: 420px</small>
                    </label>
                    <label>Largura customizada da midia (px)
                        <input type="number" name="split_width" min="0" max="2000" value="0">
                        <small class="size-preview">0 = usar Pequeno/Medio/Grande</small>
                    </label>
                    <label>Altura customizada da midia (px)
                        <input type="number" name="split_height" min="0" max="2000" value="0">
                        <small class="size-preview">0 = altura automatica</small>
                    </label>
                    <label>Encaixe da imagem/video lateral
                        <select name="split_fit">
                            <option value="cover">Preencher area (corta bordas)</option>
                            <option value="contain">Mostrar imagem inteira</option>
                        </select>
                    </label>
                </div>
                <p class="muted-note">Na criacao, salve a aba e depois edite para enviar o arquivo da imagem/video lateral.</p>
            </div>
            <div class="full mode-config-group" data-section-function="background_media">
                <div class="section-config-title">3. Titulo + texto com imagem/video no fundo completo</div>
                <div class="feature-grid">
                    <label>Midia de fundo
                        <select name="background_media_type">
                            <option value="image" selected>Imagem de fundo com texto por cima</option>
                            <option value="video">Video de fundo com texto por cima</option>
                        </select>
                    </label>
                </div>
                <p class="muted-note">Na criacao, salve a aba e depois edite para enviar a imagem/video de fundo.</p>
            </div>
            <div class="full mode-config-group" data-section-function="basic_text split_media background_media">
                <div class="section-config-title">Conteudo e estilo do texto</div>
                <div class="feature-grid">
                    <label class="full">Conteudo
                        <div class="editor-toolbar">
                            <button type="button" class="format-btn" data-command="bold">Negrito</button>
                            <button type="button" class="format-btn" data-command="italic">Italico</button>
                            <button type="button" class="format-btn" data-command="underline">Sublinhado</button>
                            <button type="button" class="format-btn" data-command="insertUnorderedList">Lista</button>
                        </div>
                        <textarea name="content" rows="4" class="rich-text-target"></textarea>
                    </label>
                    <label>Cor do texto da aba
                        <input type="color" name="text_color" value="#102133">
                    </label>
                    <label>Fonte do titulo da aba
                        <select name="title_font">
                            <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                <option value="<?= e($fontKey) ?>" <?= $fontKey === 'sora' ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Tamanho do titulo da aba (px)
                        <input type="number" name="title_size" min="12" max="252" value="40">
                    </label>
                    <label>Fonte do texto
                        <select name="text_font">
                            <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                <option value="<?= e($fontKey) ?>"><?= e(ucfirst($fontKey)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Tamanho da fonte (px)
                        <input type="number" name="text_size" min="12" max="252" value="18">
                    </label>
                </div>
            </div>
            <div class="full feature-config-group" data-section-function="cards_media cards_text">
                <div class="section-config-title">4 e 5. Configuracao de cards</div>
                <div class="feature-grid">
                    <label>Titulo da secao de cards
                        <input type="text" name="cards_title" placeholder="Nossos servicos">
                    </label>
                </div>
                <p class="muted-note">Depois de criar, edite a aba para cadastrar os cards (com imagem ou so texto).</p>
            </div>
            <div class="full feature-config-group" data-section-function="linked_gallery">
                <div class="section-config-title">6. Imagens com links</div>
                <div class="feature-grid">
                    <label>Quantidade maxima de imagens na galeria desta aba
                        <input type="number" name="linked_images_limit" min="1" max="30" value="6">
                    </label>
                    <label>Exibicao da galeria de imagens com links
                        <select name="linked_images_layout">
                            <option value="boxed" selected>Dentro da caixa de conteudo (padrao)</option>
                            <option value="direct">Direto na aba (so titulo + imagens)</option>
                        </select>
                    </label>
                </div>
                <p class="muted-note">Depois de criar a aba, edite-a para enviar as imagens da galeria.</p>
            </div>
            <div class="full feature-config-group" data-section-function="youtube">
                <div class="section-config-title">7. Video do YouTube</div>
                <div class="feature-grid">
                    <label>Link do YouTube (URL ou ID)
                        <input type="text" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                    </label>
                </div>
            </div>
            <div class="full feature-config-group" data-section-function="map">
                <div class="section-config-title">8. Me localize (Google Maps)</div>
                <div class="feature-grid">
                    <label>Link de incorporacao do Google Maps
                        <input type="text" name="map_embed_url" placeholder="https://www.google.com/maps/embed?...">
                    </label>
                </div>
            </div>
            <div class="full feature-config-group" data-section-function="contact_form">
                <div class="section-config-title">9. Formulario de contato</div>
                <div class="feature-grid">
                    <label>Titulo do formulario
                        <input type="text" name="contact_form_title" placeholder="Envie sua mensagem">
                    </label>
                    <label>E-mail de destino
                        <input type="email" name="contact_destination_email" placeholder="contato@empresa.com">
                    </label>
                    <label>Texto do botao
                        <input type="text" name="contact_button_text" placeholder="Enviar">
                    </label>
                </div>
            </div>
            <div class="full">
                <button type="submit">Adicionar aba</button>
            </div>
        </form>
        </div>
    </section>

    <section class="card panel-card <?= $activePanel === 'secoes' ? 'is-active' : '' ?>" data-panel="secoes">
        <div class="panel-head">
            <h2>Abas existentes</h2>
            <button type="button" class="collapse-toggle" data-label-open="Minimizar" data-label-closed="Expandir">Minimizar</button>
        </div>
        <div class="panel-body">
        <?php if (count($sections) === 0): ?>
            <p>Nenhuma aba cadastrada.</p>
        <?php endif; ?>

        <?php foreach ($sections as $i => $section): ?>
            <?php $isCurrentSection = $activePanel === 'secoes' && $activeSectionId === (int)$section['id']; ?>
            <?php
            $sectionFunctionValue = normalize_section_function($section['section_function'] ?? derive_section_function_from_section($section));
            $splitMediaTypeValue = normalize_media_type($section['split_media_type'] ?? (($section['section_mode'] ?? 'text') === 'video_split' ? 'video' : 'image'));
            $backgroundMediaTypeValue = normalize_media_type($section['background_media_type'] ?? (in_array(($section['section_mode'] ?? 'text'), ['video_background', 'video', 'video_full'], true) ? 'video' : 'image'));
            ?>
            <article class="section-item <?= $isCurrentSection ? '' : 'is-collapsed' ?>" data-section-id="<?= (int)$section['id'] ?>">
                <div class="section-header">
                    <h3><?= e($section['title'] ?? 'Secao') ?></h3>
                    <div class="section-actions">
                        <button type="button" class="mini-toggle" data-label-open="Minimizar" data-label-closed="Expandir">Expandir</button>
                        <form method="post">
                            <input type="hidden" name="action" value="move_section">
                            <input type="hidden" name="id" value="<?= (int)$section['id'] ?>">
                            <input type="hidden" name="direction" value="up">
                            <input type="hidden" name="return_panel" value="secoes">
                            <input type="hidden" name="return_section_id" value="<?= (int)$section['id'] ?>">
                            <button type="submit" <?= $i === 0 ? 'disabled' : '' ?>>Subir</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="action" value="move_section">
                            <input type="hidden" name="id" value="<?= (int)$section['id'] ?>">
                            <input type="hidden" name="direction" value="down">
                            <input type="hidden" name="return_panel" value="secoes">
                            <input type="hidden" name="return_section_id" value="<?= (int)$section['id'] ?>">
                            <button type="submit" <?= $i === count($sections) - 1 ? 'disabled' : '' ?>>Descer</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Deseja remover esta aba?');">
                            <input type="hidden" name="action" value="delete_section">
                            <input type="hidden" name="id" value="<?= (int)$section['id'] ?>">
                            <input type="hidden" name="return_panel" value="secoes">
                            <input type="hidden" name="return_section_id" value="<?= (int)$section['id'] ?>">
                            <button type="submit" class="danger">Excluir</button>
                        </form>
                    </div>
                </div>

                <div class="section-body">
                <form method="post" enctype="multipart/form-data" class="grid-2 section-form">
                    <input type="hidden" name="action" value="update_section">
                    <input type="hidden" name="id" value="<?= (int)$section['id'] ?>">
                    <input type="hidden" name="return_panel" value="secoes">
                    <input type="hidden" name="return_section_id" value="<?= (int)$section['id'] ?>">

                    <div class="full section-config-title">Dados basicos da aba</div>
                    <label>Titulo
                        <input type="text" name="title" value="<?= e($section['title'] ?? '') ?>" required>
                    </label>
                    <label>Nome no menu
                        <input type="text" name="menu_label" value="<?= e($section['menu_label'] ?? '') ?>" required>
                    </label>
                    <label>Slug
                        <input type="text" name="slug" value="<?= e($section['slug'] ?? '') ?>" required>
                    </label>
                    <label>Animacao desta aba
                        <select name="section_animation">
                            <option value="inherit" <?= ($section['section_animation'] ?? 'inherit') === 'inherit' ? 'selected' : '' ?>>Herdar do geral</option>
                            <option value="slide" <?= ($section['section_animation'] ?? '') === 'slide' ? 'selected' : '' ?>>Slide suave</option>
                            <option value="fade" <?= ($section['section_animation'] ?? '') === 'fade' ? 'selected' : '' ?>>Fade</option>
                            <option value="zoom" <?= ($section['section_animation'] ?? '') === 'zoom' ? 'selected' : '' ?>>Zoom in</option>
                            <option value="lift" <?= ($section['section_animation'] ?? '') === 'lift' ? 'selected' : '' ?>>Lift</option>
                            <option value="left" <?= ($section['section_animation'] ?? '') === 'left' ? 'selected' : '' ?>>Vem da esquerda</option>
                            <option value="right" <?= ($section['section_animation'] ?? '') === 'right' ? 'selected' : '' ?>>Vem da direita</option>
                            <option value="top" <?= ($section['section_animation'] ?? '') === 'top' ? 'selected' : '' ?>>Vem de cima</option>
                            <option value="letters" <?= ($section['section_animation'] ?? '') === 'letters' ? 'selected' : '' ?>>Animacao de letras</option>
                            <option value="none" <?= ($section['section_animation'] ?? '') === 'none' ? 'selected' : '' ?>>Sem animacao</option>
                        </select>
                    </label>
                    <div class="full section-config-title">Funcoes da aba (principal + extra)</div>
                    <label class="full">Selecione a funcao desta aba
                        <select name="section_function" class="section-function-select">
                            <option value="basic_text" <?= $sectionFunctionValue === 'basic_text' ? 'selected' : '' ?>>1. Titulo e texto (fundo por cor)</option>
                            <option value="split_media" <?= $sectionFunctionValue === 'split_media' ? 'selected' : '' ?>>2. Titulo + texto com imagem/video lateral</option>
                            <option value="background_media" <?= $sectionFunctionValue === 'background_media' ? 'selected' : '' ?>>3. Titulo + texto com imagem/video no fundo</option>
                            <option value="cards_media" <?= $sectionFunctionValue === 'cards_media' ? 'selected' : '' ?>>4. Cards com texto e imagens</option>
                            <option value="cards_text" <?= $sectionFunctionValue === 'cards_text' ? 'selected' : '' ?>>5. Cards com titulos e textos</option>
                            <option value="linked_gallery" <?= $sectionFunctionValue === 'linked_gallery' ? 'selected' : '' ?>>6. Imagens com links</option>
                            <option value="youtube" <?= $sectionFunctionValue === 'youtube' ? 'selected' : '' ?>>7. Video do YouTube</option>
                            <option value="map" <?= $sectionFunctionValue === 'map' ? 'selected' : '' ?>>8. Mapa do Google Maps</option>
                            <option value="contact_form" <?= $sectionFunctionValue === 'contact_form' ? 'selected' : '' ?>>9. Formulario de contato</option>
                        </select>
                        <small class="size-preview">Ao escolher a funcao, a caixa de configuracao correspondente aparece abaixo.</small>
                    </label>
                    <div class="full mode-config-group" data-section-function="basic_text">
                        <div class="section-config-title">1. Titulo e texto com configuracao da cor de fundo</div>
                        <p class="muted-note">A cor de fundo fica no bloco "Dados basicos da aba".</p>
                    </div>
                    <div class="full mode-config-group" data-section-function="split_media">
                        <div class="section-config-title">2. Titulo + texto com imagem/video lateral</div>
                        <div class="feature-grid">
                            <label>Midia lateral
                                <select name="split_media_type">
                                    <option value="image" <?= $splitMediaTypeValue === 'image' ? 'selected' : '' ?>>Imagem lateral</option>
                                    <option value="video" <?= $splitMediaTypeValue === 'video' ? 'selected' : '' ?>>Video lateral</option>
                                </select>
                            </label>
                            <label>Layout da aba
                                <select name="layout_mode">
                                    <option value="split-left" <?= ($section['layout_mode'] ?? '') === 'split-left' ? 'selected' : '' ?>>Imagem/video esquerda / texto direita</option>
                                    <option value="split-right" <?= ($section['layout_mode'] ?? 'split-right') === 'split-right' ? 'selected' : '' ?>>Texto esquerda / imagem/video direita</option>
                                </select>
                            </label>
                            <label>Tamanho da imagem/video lateral
                                <select name="split_size">
                                    <option value="small" <?= ($section['split_size'] ?? '') === 'small' ? 'selected' : '' ?>>Pequeno</option>
                                    <option value="medium" <?= ($section['split_size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medio</option>
                                    <option value="large" <?= ($section['split_size'] ?? '') === 'large' ? 'selected' : '' ?>>Grande</option>
                                </select>
                                <small class="size-preview">Largura estimada: <?= ($section['split_size'] ?? 'medium') === 'small' ? '280px' : (($section['split_size'] ?? 'medium') === 'large' ? '560px' : '420px') ?></small>
                            </label>
                            <label>Largura customizada da midia (px)
                                <input type="number" name="split_width" min="0" max="2000" value="<?= (int)($section['split_width'] ?? 0) ?>">
                                <small class="size-preview">0 = usar Pequeno/Medio/Grande</small>
                            </label>
                            <label>Altura customizada da midia (px)
                                <input type="number" name="split_height" min="0" max="2000" value="<?= (int)($section['split_height'] ?? 0) ?>">
                                <small class="size-preview">0 = altura automatica</small>
                            </label>
                            <label>Encaixe da imagem/video lateral
                                <select name="split_fit">
                                    <option value="cover" <?= ($section['split_fit'] ?? 'cover') === 'cover' ? 'selected' : '' ?>>Preencher area (corta bordas)</option>
                                    <option value="contain" <?= ($section['split_fit'] ?? '') === 'contain' ? 'selected' : '' ?>>Mostrar imagem inteira</option>
                                </select>
                            </label>
                        </div>
                        <div class="feature-grid" data-split-media-group="image">
                            <label>Imagem lateral (JPG, PNG, WEBP)
                                <input type="file" name="split_image_<?= (int)$section['id'] ?>" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                            <label class="inline-check">
                                <input type="checkbox" name="remove_split_image" value="1">
                                <span>Remover imagem lateral</span>
                            </label>
                        </div>
                        <div class="feature-grid" data-split-media-group="video">
                            <label>Video lateral da aba (MP4, WEBM, OGG)
                                <input type="file" name="section_video_<?= (int)$section['id'] ?>" accept=".mp4,.webm,.ogg">
                            </label>
                            <label class="inline-check">
                                <input type="checkbox" name="remove_section_video" value="1">
                                <span>Remover video lateral</span>
                            </label>
                        </div>
                        <?php if (!empty($section['split_image'])): ?>
                            <div class="full preview-wrap" data-split-media-group="image">
                                <img src="../<?= e($section['split_image']) ?>" alt="Imagem lateral da secao" class="preview">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($section['section_video'])): ?>
                            <div class="full preview-wrap" data-split-media-group="video">
                                <video controls class="preview" src="../<?= e($section['section_video']) ?>"></video>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="full mode-config-group" data-section-function="background_media">
                        <div class="section-config-title">3. Titulo + texto com imagem/video no fundo completo</div>
                        <div class="feature-grid">
                            <label>Midia de fundo
                                <select name="background_media_type">
                                    <option value="image" <?= $backgroundMediaTypeValue === 'image' ? 'selected' : '' ?>>Imagem de fundo com texto por cima</option>
                                    <option value="video" <?= $backgroundMediaTypeValue === 'video' ? 'selected' : '' ?>>Video de fundo com texto por cima</option>
                                </select>
                            </label>
                        </div>
                        <div class="feature-grid" data-background-media-group="image">
                            <label>Imagem de fundo (JPG, PNG, WEBP)
                                <input type="file" name="background_image_<?= (int)$section['id'] ?>" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                            <label class="inline-check">
                                <input type="checkbox" name="remove_image" value="1">
                                <span>Remover imagem de fundo</span>
                            </label>
                        </div>
                        <div class="feature-grid" data-background-media-group="video">
                            <label>Video de fundo da aba (MP4, WEBM, OGG)
                                <input type="file" name="section_video_bg_<?= (int)$section['id'] ?>" accept=".mp4,.webm,.ogg">
                            </label>
                            <label class="inline-check">
                                <input type="checkbox" name="remove_section_video" value="1">
                                <span>Remover video de fundo</span>
                            </label>
                        </div>
                        <?php if (!empty($section['background_image'])): ?>
                            <div class="full preview-wrap" data-background-media-group="image">
                                <img src="../<?= e($section['background_image']) ?>" alt="Imagem de fundo da secao" class="preview">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($section['section_video'])): ?>
                            <div class="full preview-wrap" data-background-media-group="video">
                                <video controls class="preview" src="../<?= e($section['section_video']) ?>"></video>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="full mode-config-group" data-section-function="basic_text split_media background_media">
                        <div class="section-config-title">Conteudo e estilo do texto</div>
                        <div class="feature-grid">
                            <label class="full">Texto da aba
                                <div class="editor-toolbar">
                                    <button type="button" class="format-btn" data-command="bold">Negrito</button>
                                    <button type="button" class="format-btn" data-command="italic">Italico</button>
                                    <button type="button" class="format-btn" data-command="underline">Sublinhado</button>
                                    <button type="button" class="format-btn" data-command="insertUnorderedList">Lista</button>
                                    <button type="button" class="format-btn" data-command="insertOrderedList">Lista numerada</button>
                                </div>
                                <textarea name="content" rows="4" class="rich-text-target"><?= e($section['content'] ?? '') ?></textarea>
                            </label>
                            <label>Cor do texto da aba
                                <input type="color" name="text_color" value="<?= e($section['text_color'] ?? '#102133') ?>">
                            </label>
                            <label>Fonte do titulo da aba
                                <select name="title_font">
                                    <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                        <option value="<?= e($fontKey) ?>" <?= ($section['title_font'] ?? 'sora') === $fontKey ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>Tamanho do titulo da aba (px)
                                <input type="number" name="title_size" min="12" max="252" value="<?= (int)($section['title_size'] ?? 40) ?>">
                            </label>
                            <label>Fonte do texto
                                <select name="text_font">
                                    <?php foreach ($fontOptions as $fontKey => $fontCss): ?>
                                        <option value="<?= e($fontKey) ?>" <?= ($section['text_font'] ?? 'manrope') === $fontKey ? 'selected' : '' ?>><?= e(ucfirst($fontKey)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>Tamanho da fonte (px)
                                <input type="number" name="text_size" min="12" max="252" value="<?= (int)($section['text_size'] ?? 18) ?>">
                            </label>
                        </div>
                    </div>
                    <div class="full feature-config-group" data-section-function="cards_media cards_text">
                        <div class="section-config-title">4 e 5. Configuracao de cards</div>
                        <div class="feature-grid">
                            <label>Titulo da secao de cards
                                <input type="text" name="cards_title" value="<?= e($section['cards_title'] ?? '') ?>" placeholder="Destinos em destaque">
                            </label>
                        </div>
                    </div>
                    <div class="full feature-config-group" data-section-function="linked_gallery">
                        <div class="section-config-title">6. Imagens com links</div>
                        <div class="feature-grid">
                            <label>Quantidade maxima de imagens na galeria desta aba
                                <input type="number" name="linked_images_limit" min="1" max="30" value="<?= (int)normalize_linked_images_limit($section['linked_images_limit'] ?? 6) ?>">
                            </label>
                            <label>Exibicao da galeria de imagens com links
                                <select name="linked_images_layout">
                                    <option value="boxed" <?= ($section['linked_images_layout'] ?? 'boxed') === 'boxed' ? 'selected' : '' ?>>Dentro da caixa de conteudo (padrao)</option>
                                    <option value="direct" <?= ($section['linked_images_layout'] ?? '') === 'direct' ? 'selected' : '' ?>>Direto na aba (so titulo + imagens)</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="full feature-config-group" data-section-function="youtube">
                        <div class="section-config-title">7. Video do YouTube</div>
                        <div class="feature-grid">
                            <label>Link do YouTube (URL ou ID)
                                <input type="text" name="youtube_url" value="<?= e($section['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
                            </label>
                        </div>
                    </div>
                    <div class="full feature-config-group" data-section-function="map">
                        <div class="section-config-title">8. Me localize (Google Maps)</div>
                        <div class="feature-grid">
                            <label>Link de incorporacao do Google Maps
                                <input type="text" name="map_embed_url" value="<?= e($section['map_embed_url'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?...">
                            </label>
                        </div>
                    </div>
                    <div class="full feature-config-group" data-section-function="contact_form">
                        <div class="section-config-title">9. Formulario de contato</div>
                        <div class="feature-grid">
                            <label>Titulo do formulario
                                <input type="text" name="contact_form_title" value="<?= e($section['contact_form_title'] ?? 'Envie sua mensagem') ?>" placeholder="Envie sua mensagem">
                            </label>
                            <label>E-mail de destino
                                <input type="email" name="contact_destination_email" value="<?= e($section['contact_destination_email'] ?? '') ?>" placeholder="contato@empresa.com">
                            </label>
                            <label>Texto do botao
                                <input type="text" name="contact_button_text" value="<?= e($section['contact_button_text'] ?? 'Enviar') ?>" placeholder="Enviar">
                            </label>
                        </div>
                    </div>

                    <div class="full feature-config-group" data-section-function="linked_gallery">
                        <div class="section-config-title">Gerenciar: Galeria de Imagens com Links</div>
                        <?php
                        $linkedImages = is_array($section['linked_images'] ?? null) ? $section['linked_images'] : [];
                        $linkedImagesLimit = normalize_linked_images_limit($section['linked_images_limit'] ?? 6);
                        ?>
                        <p class="full muted-note">Imagens atuais: <?= count($linkedImages) ?> de <?= $linkedImagesLimit ?>.</p>
                        <p class="full muted-note">Limites atuais do servidor: upload_max_filesize=<?= e($uploadMaxFilesize) ?> e post_max_size=<?= e($postMaxSize) ?>.</p>
                        <?php if (count($linkedImages) === 0): ?>
                            <p class="full muted-note">Nenhuma imagem vinculada nesta aba ainda.</p>
                        <?php endif; ?>
                        <?php foreach ($linkedImages as $linkedImage): ?>
                            <?php
                            $linkedImageId = (int)($linkedImage['id'] ?? 0);
                            if ($linkedImageId <= 0) {
                                continue;
                            }
                            $linkedImageLink = (string)($linkedImage['link'] ?? '');
                            $linkedImageAlt = (string)($linkedImage['alt'] ?? '');
                            $linkedImagePath = (string)($linkedImage['image'] ?? '');
                            ?>
                            <input type="hidden" name="linked_image_ids[]" value="<?= $linkedImageId ?>">
                            <label>Link da imagem #<?= $linkedImageId ?>
                                <input type="url" name="linked_image_links[]" value="<?= e($linkedImageLink) ?>" placeholder="https://seusite.com/pagina">
                            </label>
                            <label>Texto ALT da imagem #<?= $linkedImageId ?>
                                <input type="text" name="linked_image_alts[]" value="<?= e($linkedImageAlt) ?>" placeholder="Descricao da imagem">
                            </label>
                            <label>Trocar arquivo da imagem #<?= $linkedImageId ?> (JPG, PNG, WEBP)
                                <input type="file" name="linked_image_files[]" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                            <label class="inline-check">
                                <input type="checkbox" name="linked_image_remove_ids[]" value="<?= $linkedImageId ?>">
                                <span>Remover imagem #<?= $linkedImageId ?></span>
                            </label>
                            <?php if ($linkedImagePath !== ''): ?>
                                <div class="full preview-wrap">
                                    <?php if ($linkedImageLink !== ''): ?>
                                        <a href="<?= e($linkedImageLink) ?>" target="_blank" rel="noopener">
                                            <img src="../<?= e($linkedImagePath) ?>" alt="<?= e($linkedImageAlt !== '' ? $linkedImageAlt : 'Imagem vinculada') ?>" class="preview linked-preview">
                                        </a>
                                    <?php else: ?>
                                        <img src="../<?= e($linkedImagePath) ?>" alt="<?= e($linkedImageAlt !== '' ? $linkedImageAlt : 'Imagem vinculada') ?>" class="preview linked-preview">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (count($linkedImages) < $linkedImagesLimit): ?>
                            <label>Nova imagem da galeria (JPG, PNG, WEBP)
                                <input type="file" name="linked_image_new_file" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                            <label>Link da nova imagem
                                <input type="url" name="linked_image_new_link" placeholder="https://seusite.com/pagina">
                            </label>
                            <label>Texto ALT da nova imagem
                                <input type="text" name="linked_image_new_alt" placeholder="Descricao da imagem">
                            </label>
                        <?php else: ?>
                            <p class="full muted-note">Limite da galeria atingido para esta aba. Aumente a quantidade maxima ou remova uma imagem.</p>
                        <?php endif; ?>
                    </div>
                    <div class="full feature-config-group" data-section-function="cards_media cards_text">
                        <div class="section-config-title">Gerenciar: Cards (maximo 6)</div>
                        <?php
                        $cardsItems = is_array($section['cards_items'] ?? null) ? $section['cards_items'] : [];
                        $cardsItems = array_slice($cardsItems, 0, 6);
                        ?>
                        <p class="full muted-note">Cards atuais: <?= count($cardsItems) ?> de 6.</p>
                        <?php if (count($cardsItems) === 0): ?>
                            <p class="full muted-note">Nenhum card cadastrado nesta aba ainda.</p>
                        <?php endif; ?>
                        <?php foreach ($cardsItems as $cardItem): ?>
                            <?php
                            $cardId = (int)($cardItem['id'] ?? 0);
                            if ($cardId <= 0) {
                                continue;
                            }
                            $cardTitle = (string)($cardItem['title'] ?? '');
                            $cardText = (string)($cardItem['text'] ?? '');
                            $cardImagePath = (string)($cardItem['image'] ?? '');
                            $cardButtonText = (string)($cardItem['button_text'] ?? '');
                            $cardButtonLink = (string)($cardItem['button_link'] ?? '');
                            ?>
                            <input type="hidden" name="card_ids[]" value="<?= $cardId ?>">
                            <label>Titulo do card #<?= $cardId ?>
                                <input type="text" name="card_titles[]" value="<?= e($cardTitle) ?>" placeholder="Titulo do card">
                            </label>
                            <label>Descricao do card #<?= $cardId ?>
                                <textarea name="card_texts[]" rows="4" placeholder="Descricao do card"><?= e($cardText) ?></textarea>
                            </label>
                            <label>Texto do botao do card #<?= $cardId ?>
                                <input type="text" name="card_button_texts[]" value="<?= e($cardButtonText) ?>" placeholder="Saiba mais">
                            </label>
                            <label>Link do botao do card #<?= $cardId ?>
                                <input type="url" name="card_button_links[]" value="<?= e($cardButtonLink) ?>" placeholder="https://seusite.com/pagina">
                            </label>
                            <label data-section-function="cards_media">Trocar imagem do card #<?= $cardId ?> (JPG, PNG, WEBP)
                                <input type="file" name="card_image_files[]" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                            <label class="inline-check" data-section-function="cards_media">
                                <input type="checkbox" name="card_remove_image_ids[]" value="<?= $cardId ?>">
                                <span>Remover imagem do card #<?= $cardId ?></span>
                            </label>
                            <label class="inline-check">
                                <input type="checkbox" name="card_remove_ids[]" value="<?= $cardId ?>">
                                <span>Remover card #<?= $cardId ?></span>
                            </label>
                            <?php if ($cardImagePath !== ''): ?>
                                <div class="full preview-wrap" data-section-function="cards_media">
                                    <img src="../<?= e($cardImagePath) ?>" alt="<?= e($cardTitle !== '' ? $cardTitle : 'Imagem do card') ?>" class="preview linked-preview">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (count($cardsItems) < 6): ?>
                            <label>Titulo do novo card
                                <input type="text" name="card_new_title" placeholder="Titulo do card">
                            </label>
                            <label>Descricao do novo card
                                <textarea name="card_new_text" rows="4" placeholder="Descricao do card"></textarea>
                            </label>
                            <label>Texto do botao do novo card
                                <input type="text" name="card_new_button_text" placeholder="Saiba mais">
                            </label>
                            <label>Link do botao do novo card
                                <input type="url" name="card_new_button_link" placeholder="https://seusite.com/pagina">
                            </label>
                            <label data-section-function="cards_media">Imagem do novo card (opcional, JPG, PNG, WEBP)
                                <input type="file" name="card_new_file" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                        <?php else: ?>
                            <p class="full muted-note">Limite de cards atingido para esta aba. Remova um card para adicionar outro.</p>
                        <?php endif; ?>
                    </div>

                    <label>Cor de fundo da aba
                        <input type="color" name="background_color" value="<?= e($section['background_color'] ?? '#ffffff') ?>">
                    </label>
                    <div class="full">
                        <button type="submit">Salvar aba</button>
                    </div>
                </form>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </section>

    <section class="card panel-card <?= $activePanel === 'acesso' ? 'is-active' : '' ?>" data-panel="acesso">
        <div class="panel-head">
            <h2>Acesso ao dashboard</h2>
            <button type="button" class="collapse-toggle" data-label-open="Minimizar" data-label-closed="Expandir">Minimizar</button>
        </div>
        <div class="panel-body">
        <form method="post" class="grid-2">
            <input type="hidden" name="action" value="update_admin">
            <input type="hidden" name="return_panel" value="acesso">
            <label>Usuario
                <input type="text" name="admin_user" value="<?= e($data['admin']['username'] ?? 'admin') ?>" required>
            </label>
            <label>Nova senha (deixe vazio para manter)
                <input type="password" name="admin_pass">
            </label>
            <div class="full">
                <button type="submit">Atualizar acesso</button>
            </div>
        </form>
        </div>
    </section>
</main>
</div>
<script src="../assets/admin.js"></script>
<script src="../assets/dashboard-ui.js"></script>
</body>
</html>
