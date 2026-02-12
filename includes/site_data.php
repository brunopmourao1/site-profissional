<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function default_site_data(): array
{
    return [
        'meta' => [
            'site_name' => 'Minha Empresa',
            'brand_display' => 'both',
            'logo_image' => '',
            'logo_alt' => 'Logo da empresa',
            'hero_video' => '',
            'hero_image' => '',
            'hero_mode' => 'text',
            'hero_layout' => 'split-right',
            'hero_split_size' => 'medium',
            'hero_split_fit' => 'cover',
            'hero_split_width' => 0,
            'hero_split_height' => 0,
            'hero_overline' => 'Apresentacao profissional',
            'headline' => 'Solucoes profissionais para o seu negocio',
            'hero_text_color' => '#f6f2eb',
            'intro' => 'Apresente aqui a proposta de valor da sua empresa em poucas linhas.',
            'brand_font' => 'sora',
            'brand_size' => 18,
            'headline_font' => 'sora',
            'headline_size' => 56,
            'intro_font' => 'manrope',
            'intro_size' => 17,
            'footer_font' => 'manrope',
            'footer_size' => 16,
            'animation_style' => 'slide',
            'hero_motion' => 'soft',
            'primary_color' => '#113946',
            'secondary_color' => '#bca37f',
            'contact_phone' => '(00) 00000-0000',
            'contact_email' => 'contato@empresa.com',
            'address' => 'Rua Exemplo, 123 - Cidade/UF',
            'whatsapp' => '5500000000000',
            'social_facebook_enabled' => false,
            'social_facebook_url' => '',
            'social_instagram_enabled' => false,
            'social_instagram_url' => '',
            'social_linkedin_enabled' => false,
            'social_linkedin_url' => '',
            'social_behance_enabled' => false,
            'social_behance_url' => '',
            'social_youtube_enabled' => false,
            'social_youtube_url' => '',
        ],
        'admin' => [
            'username' => 'admin',
            'password_hash' => '',
            'password_plain' => 'admin123',
        ],
        'sections' => [
            [
                'id' => 1,
                'title' => 'Quem somos',
                'slug' => 'quem-somos',
                'content' => 'Conte aqui a historia da empresa, visao, missao e diferenciais.',
                'text_color' => '#102133',
                'title_font' => 'sora',
                'title_size' => 40,
                'text_font' => 'manrope',
                'text_size' => 18,
                'layout_mode' => 'background',
                'split_image' => '',
                'split_size' => 'medium',
                'split_fit' => 'cover',
                'split_width' => 0,
                'split_height' => 0,
                'section_animation' => 'inherit',
                'section_video' => '',
                'section_mode' => 'text',
                'section_function' => 'basic_text',
                'split_media_type' => 'image',
                'background_media_type' => 'image',
                'section_feature' => 'none',
                'youtube_enabled' => false,
                'youtube_url' => '',
                'map_enabled' => false,
                'map_embed_url' => '',
                'contact_enabled' => false,
                'contact_form_title' => 'Envie sua mensagem',
                'contact_destination_email' => '',
                'contact_button_text' => 'Enviar',
                'linked_images' => [],
                'linked_images_limit' => 6,
                'linked_images_layout' => 'boxed',
                'cards_title' => '',
                'cards_style' => 'media',
                'cards_spacing' => 'spaced',
                'cards_limit' => 6,
                'cards_items' => [],
                'background_color' => '#f6f4ef',
                'background_image' => '',
                'menu_label' => 'Quem Somos',
                'order' => 1,
            ],
            [
                'id' => 2,
                'title' => 'Atividades',
                'slug' => 'atividades',
                'content' => 'Liste os principais servicos e especialidades que sua empresa oferece.',
                'text_color' => '#102133',
                'title_font' => 'sora',
                'title_size' => 40,
                'text_font' => 'manrope',
                'text_size' => 18,
                'layout_mode' => 'background',
                'split_image' => '',
                'split_size' => 'medium',
                'split_fit' => 'cover',
                'split_width' => 0,
                'split_height' => 0,
                'section_animation' => 'inherit',
                'section_video' => '',
                'section_mode' => 'text',
                'section_function' => 'basic_text',
                'split_media_type' => 'image',
                'background_media_type' => 'image',
                'section_feature' => 'none',
                'youtube_enabled' => false,
                'youtube_url' => '',
                'map_enabled' => false,
                'map_embed_url' => '',
                'contact_enabled' => false,
                'contact_form_title' => 'Envie sua mensagem',
                'contact_destination_email' => '',
                'contact_button_text' => 'Enviar',
                'linked_images' => [],
                'linked_images_limit' => 6,
                'linked_images_layout' => 'boxed',
                'cards_title' => '',
                'cards_style' => 'media',
                'cards_spacing' => 'spaced',
                'cards_limit' => 6,
                'cards_items' => [],
                'background_color' => '#ffffff',
                'background_image' => '',
                'menu_label' => 'Atividades',
                'order' => 2,
            ],
            [
                'id' => 3,
                'title' => 'Clientes',
                'slug' => 'clientes',
                'content' => 'Apresente seus principais clientes, segmentos atendidos e resultados entregues.',
                'text_color' => '#102133',
                'title_font' => 'sora',
                'title_size' => 40,
                'text_font' => 'manrope',
                'text_size' => 18,
                'layout_mode' => 'background',
                'split_image' => '',
                'split_size' => 'medium',
                'split_fit' => 'cover',
                'split_width' => 0,
                'split_height' => 0,
                'section_animation' => 'inherit',
                'section_video' => '',
                'section_mode' => 'text',
                'section_function' => 'basic_text',
                'split_media_type' => 'image',
                'background_media_type' => 'image',
                'section_feature' => 'none',
                'youtube_enabled' => false,
                'youtube_url' => '',
                'map_enabled' => false,
                'map_embed_url' => '',
                'contact_enabled' => false,
                'contact_form_title' => 'Envie sua mensagem',
                'contact_destination_email' => '',
                'contact_button_text' => 'Enviar',
                'linked_images' => [],
                'linked_images_limit' => 6,
                'linked_images_layout' => 'boxed',
                'cards_title' => '',
                'cards_style' => 'media',
                'cards_spacing' => 'spaced',
                'cards_limit' => 6,
                'cards_items' => [],
                'background_color' => '#f6f4ef',
                'background_image' => '',
                'menu_label' => 'Clientes',
                'order' => 3,
            ],
            [
                'id' => 4,
                'title' => 'Localizacao e contato',
                'slug' => 'contato',
                'content' => 'Inclua localizacao, formas de contato e horario de atendimento.',
                'text_color' => '#102133',
                'title_font' => 'sora',
                'title_size' => 40,
                'text_font' => 'manrope',
                'text_size' => 18,
                'layout_mode' => 'background',
                'split_image' => '',
                'split_size' => 'medium',
                'split_fit' => 'cover',
                'split_width' => 0,
                'split_height' => 0,
                'section_animation' => 'inherit',
                'section_video' => '',
                'section_mode' => 'text',
                'section_function' => 'basic_text',
                'split_media_type' => 'image',
                'background_media_type' => 'image',
                'section_feature' => 'none',
                'youtube_enabled' => false,
                'youtube_url' => '',
                'map_enabled' => false,
                'map_embed_url' => '',
                'contact_enabled' => false,
                'contact_form_title' => 'Envie sua mensagem',
                'contact_destination_email' => '',
                'contact_button_text' => 'Enviar',
                'linked_images' => [],
                'linked_images_limit' => 6,
                'linked_images_layout' => 'boxed',
                'cards_title' => '',
                'cards_style' => 'media',
                'cards_spacing' => 'spaced',
                'cards_limit' => 6,
                'cards_items' => [],
                'background_color' => '#ffffff',
                'background_image' => '',
                'menu_label' => 'Contato',
                'order' => 4,
            ],
        ],
    ];
}

function ensure_data_file(): void
{
    if (!is_dir(dirname(DATA_PATH))) {
        mkdir(dirname(DATA_PATH), 0775, true);
    }

    if (!file_exists(DATA_PATH)) {
        $data = default_site_data();
        file_put_contents(DATA_PATH, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

function load_site_data(): array
{
    ensure_data_file();

    $raw = file_get_contents(DATA_PATH);
    $data = json_decode((string)$raw, true);

    if (!is_array($data)) {
        $data = default_site_data();
        save_site_data($data);
    }

    if (!isset($data['sections']) || !is_array($data['sections'])) {
        $data['sections'] = [];
    }

    if (!isset($data['meta']) || !is_array($data['meta'])) {
        $data['meta'] = [];
    }
    if (!isset($data['meta']['site_name']) || !is_string($data['meta']['site_name']) || trim($data['meta']['site_name']) === '') {
        $data['meta']['site_name'] = 'Minha Empresa';
    }
    if (!isset($data['meta']['brand_display']) || !is_string($data['meta']['brand_display'])) {
        $data['meta']['brand_display'] = 'both';
    }
    $data['meta']['brand_display'] = normalize_brand_display($data['meta']['brand_display']);
    if (!isset($data['meta']['logo_image']) || !is_string($data['meta']['logo_image'])) {
        $data['meta']['logo_image'] = '';
    }
    if (!isset($data['meta']['logo_alt']) || !is_string($data['meta']['logo_alt'])) {
        $data['meta']['logo_alt'] = 'Logo da empresa';
    }
    if (!isset($data['meta']['hero_video']) || !is_string($data['meta']['hero_video'])) {
        $data['meta']['hero_video'] = '';
    }
    if (!isset($data['meta']['hero_image']) || !is_string($data['meta']['hero_image'])) {
        $data['meta']['hero_image'] = '';
    }
    if (!isset($data['meta']['hero_mode']) || !is_string($data['meta']['hero_mode'])) {
        $data['meta']['hero_mode'] = 'text';
    }
    if (!isset($data['meta']['hero_overline']) || !is_string($data['meta']['hero_overline'])) {
        $data['meta']['hero_overline'] = 'Apresentacao profissional';
    }
    if (!isset($data['meta']['hero_text_color']) || !is_string($data['meta']['hero_text_color'])) {
        $data['meta']['hero_text_color'] = '#f6f2eb';
    }
    if (!in_array($data['meta']['hero_mode'], ['text', 'video', 'video_full', 'video_background', 'video_split', 'image_full', 'image_background', 'image_split'], true)) {
        $data['meta']['hero_mode'] = 'text';
    }
    if (!isset($data['meta']['hero_layout']) || !is_string($data['meta']['hero_layout'])) {
        $data['meta']['hero_layout'] = 'split-right';
    }
    if (!in_array($data['meta']['hero_layout'], ['split-left', 'split-right'], true)) {
        $data['meta']['hero_layout'] = 'split-right';
    }
    if (!isset($data['meta']['hero_split_size']) || !is_string($data['meta']['hero_split_size'])) {
        $data['meta']['hero_split_size'] = 'medium';
    }
    if (!in_array($data['meta']['hero_split_size'], ['small', 'medium', 'large'], true)) {
        $data['meta']['hero_split_size'] = 'medium';
    }
    if (!isset($data['meta']['hero_split_fit']) || !is_string($data['meta']['hero_split_fit'])) {
        $data['meta']['hero_split_fit'] = 'cover';
    }
    if (!in_array($data['meta']['hero_split_fit'], ['cover', 'contain'], true)) {
        $data['meta']['hero_split_fit'] = 'cover';
    }
    if (!isset($data['meta']['hero_split_width']) || !is_numeric($data['meta']['hero_split_width'])) {
        $data['meta']['hero_split_width'] = 0;
    }
    if (!isset($data['meta']['hero_split_height']) || !is_numeric($data['meta']['hero_split_height'])) {
        $data['meta']['hero_split_height'] = 0;
    }
    $data['meta']['hero_split_width'] = normalize_dimension_size($data['meta']['hero_split_width']);
    $data['meta']['hero_split_height'] = normalize_dimension_size($data['meta']['hero_split_height']);
    if (!isset($data['meta']['brand_font']) || !is_string($data['meta']['brand_font'])) {
        $data['meta']['brand_font'] = 'sora';
    }
    if (!isset($data['meta']['brand_size']) || !is_numeric($data['meta']['brand_size'])) {
        $data['meta']['brand_size'] = 18;
    }
    if (!isset($data['meta']['headline_font']) || !is_string($data['meta']['headline_font'])) {
        $data['meta']['headline_font'] = 'sora';
    }
    if (!isset($data['meta']['headline_size']) || !is_numeric($data['meta']['headline_size'])) {
        $data['meta']['headline_size'] = 56;
    }
    if (!isset($data['meta']['intro_font']) || !is_string($data['meta']['intro_font'])) {
        $data['meta']['intro_font'] = 'manrope';
    }
    if (!isset($data['meta']['intro_size']) || !is_numeric($data['meta']['intro_size'])) {
        $data['meta']['intro_size'] = 17;
    }
    if (!isset($data['meta']['footer_font']) || !is_string($data['meta']['footer_font'])) {
        $data['meta']['footer_font'] = 'manrope';
    }
    if (!isset($data['meta']['footer_size']) || !is_numeric($data['meta']['footer_size'])) {
        $data['meta']['footer_size'] = 16;
    }
    if (!isset($data['meta']['animation_style']) || !is_string($data['meta']['animation_style'])) {
        $data['meta']['animation_style'] = 'slide';
    }
    if (!isset($data['meta']['hero_motion']) || !is_string($data['meta']['hero_motion'])) {
        $data['meta']['hero_motion'] = 'soft';
    }
    if (!isset($data['meta']['social_facebook_enabled'])) {
        $data['meta']['social_facebook_enabled'] = false;
    }
    if (!isset($data['meta']['social_facebook_url']) || !is_string($data['meta']['social_facebook_url'])) {
        $data['meta']['social_facebook_url'] = '';
    }
    if (!isset($data['meta']['social_instagram_enabled'])) {
        $data['meta']['social_instagram_enabled'] = false;
    }
    if (!isset($data['meta']['social_instagram_url']) || !is_string($data['meta']['social_instagram_url'])) {
        $data['meta']['social_instagram_url'] = '';
    }
    if (!isset($data['meta']['social_linkedin_enabled'])) {
        $data['meta']['social_linkedin_enabled'] = false;
    }
    if (!isset($data['meta']['social_linkedin_url']) || !is_string($data['meta']['social_linkedin_url'])) {
        $data['meta']['social_linkedin_url'] = '';
    }
    if (!isset($data['meta']['social_behance_enabled'])) {
        $data['meta']['social_behance_enabled'] = false;
    }
    if (!isset($data['meta']['social_behance_url']) || !is_string($data['meta']['social_behance_url'])) {
        $data['meta']['social_behance_url'] = '';
    }
    if (!isset($data['meta']['social_youtube_enabled'])) {
        $data['meta']['social_youtube_enabled'] = false;
    }
    if (!isset($data['meta']['social_youtube_url']) || !is_string($data['meta']['social_youtube_url'])) {
        $data['meta']['social_youtube_url'] = '';
    }

    foreach ($data['sections'] as &$section) {
        if (!isset($section['title_font']) || !is_string($section['title_font'])) {
            $section['title_font'] = 'sora';
        }
        if (!isset($section['title_size']) || !is_numeric($section['title_size'])) {
            $section['title_size'] = 40;
        }
        if (!isset($section['text_font']) || !is_string($section['text_font'])) {
            $section['text_font'] = 'manrope';
        }
        if (!isset($section['text_color']) || !is_string($section['text_color'])) {
            $section['text_color'] = '#102133';
        }
        if (!isset($section['text_size']) || !is_numeric($section['text_size'])) {
            $section['text_size'] = 18;
        }
        if (!isset($section['layout_mode']) || !is_string($section['layout_mode'])) {
            $section['layout_mode'] = 'background';
        }
        if (!isset($section['split_image']) || !is_string($section['split_image'])) {
            $section['split_image'] = '';
        }
        if (!isset($section['split_size']) || !is_string($section['split_size'])) {
            $section['split_size'] = 'medium';
        }
        if (!in_array($section['split_size'], ['small', 'medium', 'large'], true)) {
            $section['split_size'] = 'medium';
        }
        if (!isset($section['split_fit']) || !is_string($section['split_fit'])) {
            $section['split_fit'] = 'cover';
        }
        if (!isset($section['split_width']) || !is_numeric($section['split_width'])) {
            $section['split_width'] = 0;
        }
        if (!isset($section['split_height']) || !is_numeric($section['split_height'])) {
            $section['split_height'] = 0;
        }
        $section['split_width'] = normalize_dimension_size($section['split_width']);
        $section['split_height'] = normalize_dimension_size($section['split_height']);
        if (!isset($section['section_animation']) || !is_string($section['section_animation'])) {
            $section['section_animation'] = 'inherit';
        }
        if (!isset($section['section_video']) || !is_string($section['section_video'])) {
            $section['section_video'] = '';
        }
        if (!isset($section['section_mode']) || !is_string($section['section_mode'])) {
            $section['section_mode'] = 'text';
        }
        if (!in_array($section['section_mode'], ['text', 'video', 'video_full', 'video_background', 'video_split'], true)) {
            $section['section_mode'] = 'text';
        }
        if (!isset($section['section_feature']) || !is_string($section['section_feature'])) {
            $section['section_feature'] = '';
        }
        if ($section['section_feature'] === '') {
            if (isset($section['linked_images']) && is_array($section['linked_images']) && count($section['linked_images']) > 0) {
                $section['section_feature'] = 'linked_gallery';
            } elseif (isset($section['cards_items']) && is_array($section['cards_items']) && count($section['cards_items']) > 0) {
                $section['section_feature'] = 'cards';
            } elseif (!empty($section['contact_enabled'])) {
                $section['section_feature'] = 'contact_form';
            } elseif (!empty($section['map_enabled'])) {
                $section['section_feature'] = 'map';
            } elseif (!empty($section['youtube_enabled'])) {
                $section['section_feature'] = 'youtube';
            } else {
                $section['section_feature'] = 'none';
            }
        }
        if (!in_array($section['section_feature'], ['none', 'youtube', 'map', 'contact_form', 'linked_gallery', 'cards'], true)) {
            $section['section_feature'] = 'none';
        }
        if (!isset($section['section_function']) || !is_string($section['section_function'])) {
            $section['section_function'] = '';
        }
        if ($section['section_function'] === '') {
            $section['section_function'] = derive_section_function_from_section($section);
        }
        $section['section_function'] = normalize_section_function($section['section_function']);
        if (!isset($section['split_media_type']) || !is_string($section['split_media_type'])) {
            $section['split_media_type'] = '';
        }
        if ($section['split_media_type'] === '') {
            $section['split_media_type'] = (($section['section_mode'] ?? 'text') === 'video_split') ? 'video' : 'image';
        }
        $section['split_media_type'] = normalize_media_type($section['split_media_type']);
        if (!isset($section['background_media_type']) || !is_string($section['background_media_type'])) {
            $section['background_media_type'] = '';
        }
        if ($section['background_media_type'] === '') {
            $currentSectionMode = (string)($section['section_mode'] ?? 'text');
            $section['background_media_type'] = in_array($currentSectionMode, ['video_background', 'video', 'video_full'], true) ? 'video' : 'image';
        }
        $section['background_media_type'] = normalize_media_type($section['background_media_type']);
        if (!isset($section['youtube_enabled'])) {
            $section['youtube_enabled'] = false;
        }
        if (!isset($section['youtube_url']) || !is_string($section['youtube_url'])) {
            $section['youtube_url'] = '';
        }
        if (!isset($section['map_enabled'])) {
            $section['map_enabled'] = false;
        }
        if (!isset($section['map_embed_url']) || !is_string($section['map_embed_url'])) {
            $section['map_embed_url'] = '';
        }
        if (!isset($section['contact_enabled'])) {
            $section['contact_enabled'] = false;
        }
        if (!isset($section['contact_form_title']) || !is_string($section['contact_form_title']) || trim($section['contact_form_title']) === '') {
            $section['contact_form_title'] = 'Envie sua mensagem';
        }
        if (!isset($section['contact_destination_email']) || !is_string($section['contact_destination_email'])) {
            $legacyContactEmail = '';
            if (isset($section['contact_email']) && is_string($section['contact_email'])) {
                $legacyContactEmail = trim($section['contact_email']);
            }
            $section['contact_destination_email'] = $legacyContactEmail;
        }
        if (!isset($section['contact_button_text']) || !is_string($section['contact_button_text']) || trim($section['contact_button_text']) === '') {
            $section['contact_button_text'] = 'Enviar';
        }
        $section['youtube_enabled'] = $section['section_feature'] === 'youtube';
        $section['map_enabled'] = $section['section_feature'] === 'map';
        $section['contact_enabled'] = $section['section_feature'] === 'contact_form';
        if (!isset($section['linked_images']) || !is_array($section['linked_images'])) {
            $section['linked_images'] = [];
        }
        if (!isset($section['linked_images_limit']) || !is_numeric($section['linked_images_limit'])) {
            $section['linked_images_limit'] = 6;
        }
        $section['linked_images_limit'] = normalize_linked_images_limit($section['linked_images_limit']);
        if (!isset($section['linked_images_layout']) || !is_string($section['linked_images_layout'])) {
            $section['linked_images_layout'] = 'boxed';
        }
        if (!in_array($section['linked_images_layout'], ['boxed', 'direct'], true)) {
            $section['linked_images_layout'] = 'boxed';
        }
        $normalizedLinkedImages = [];
        foreach ($section['linked_images'] as $linkedImage) {
            if (!is_array($linkedImage)) {
                continue;
            }
            $imagePath = trim((string)($linkedImage['image'] ?? ''));
            if ($imagePath === '') {
                continue;
            }
            $linkedId = (int)($linkedImage['id'] ?? 0);
            if ($linkedId <= 0) {
                $linkedId = count($normalizedLinkedImages) + 1;
            }
            $normalizedLinkedImages[] = [
                'id' => $linkedId,
                'image' => $imagePath,
                'link' => trim((string)($linkedImage['link'] ?? '')),
                'alt' => trim((string)($linkedImage['alt'] ?? '')),
                'order' => count($normalizedLinkedImages) + 1,
            ];
        }
        $section['linked_images'] = $normalizedLinkedImages;

        if (!isset($section['cards_title']) || !is_string($section['cards_title'])) {
            $section['cards_title'] = '';
        }
        if (!isset($section['cards_style']) || !is_string($section['cards_style'])) {
            $section['cards_style'] = '';
        }
        if ($section['cards_style'] === '') {
            $section['cards_style'] = ($section['section_function'] === 'cards_text') ? 'text' : 'media';
        }
        if (!in_array($section['cards_style'], ['media', 'text'], true)) {
            $section['cards_style'] = 'media';
        }
        if (!isset($section['cards_spacing']) || !is_string($section['cards_spacing'])) {
            $section['cards_spacing'] = 'spaced';
        }
        $section['cards_spacing'] = normalize_cards_spacing($section['cards_spacing']);
        if (!isset($section['cards_limit']) || !is_numeric($section['cards_limit'])) {
            $section['cards_limit'] = 6;
        }
        $section['cards_limit'] = normalize_cards_limit($section['cards_limit']);
        if (!isset($section['cards_items']) || !is_array($section['cards_items'])) {
            $section['cards_items'] = [];
        }
        $normalizedCards = [];
        $maxCardId = 0;
        foreach ($section['cards_items'] as $cardItem) {
            if (!is_array($cardItem)) {
                continue;
            }
            $cardTitle = sanitize_text((string)($cardItem['title'] ?? ''));
            $cardText = sanitize_text((string)($cardItem['text'] ?? ''));
            $cardImage = sanitize_text((string)($cardItem['image'] ?? ''));
            $cardButtonText = sanitize_text((string)($cardItem['button_text'] ?? ''));
            $cardButtonLink = sanitize_text((string)($cardItem['button_link'] ?? ''));
            if ($cardTitle === '' && $cardText === '' && $cardImage === '') {
                continue;
            }
            $cardId = (int)($cardItem['id'] ?? 0);
            if ($cardId <= 0) {
                $cardId = $maxCardId + 1;
            }
            if ($cardId > $maxCardId) {
                $maxCardId = $cardId;
            }
            $normalizedCards[] = [
                'id' => $cardId,
                'title' => $cardTitle,
                'text' => $cardText,
                'image' => $cardImage,
                'button_text' => $cardButtonText,
                'button_link' => $cardButtonLink,
                'order' => count($normalizedCards) + 1,
            ];
            if (count($normalizedCards) >= $section['cards_limit']) {
                break;
            }
        }
        $section['cards_items'] = $normalizedCards;

        switch ($section['section_function']) {
            case 'split_media':
                $section['section_feature'] = 'none';
                $section['section_mode'] = $section['split_media_type'] === 'video' ? 'video_split' : 'text';
                if (!in_array((string)$section['layout_mode'], ['split-left', 'split-right'], true)) {
                    $section['layout_mode'] = 'split-right';
                }
                break;
            case 'background_media':
                $section['section_feature'] = 'none';
                $section['section_mode'] = $section['background_media_type'] === 'video' ? 'video_background' : 'text';
                $section['layout_mode'] = 'background';
                break;
            case 'cards_media':
                $section['section_feature'] = 'cards';
                $section['section_mode'] = 'text';
                $section['cards_style'] = 'media';
                $section['layout_mode'] = 'background';
                break;
            case 'cards_text':
                $section['section_feature'] = 'cards';
                $section['section_mode'] = 'text';
                $section['cards_style'] = 'text';
                $section['layout_mode'] = 'background';
                break;
            case 'linked_gallery':
                $section['section_feature'] = 'linked_gallery';
                $section['section_mode'] = 'text';
                $section['layout_mode'] = 'background';
                break;
            case 'youtube':
                $section['section_feature'] = 'youtube';
                $section['section_mode'] = 'text';
                $section['layout_mode'] = 'background';
                break;
            case 'map':
                $section['section_feature'] = 'map';
                $section['section_mode'] = 'text';
                $section['layout_mode'] = 'background';
                break;
            case 'contact_form':
                $section['section_feature'] = 'contact_form';
                $section['section_mode'] = 'text';
                $section['layout_mode'] = 'background';
                break;
            default:
                $section['section_feature'] = 'none';
                $section['section_mode'] = 'text';
                $section['layout_mode'] = 'background';
                break;
        }
        $section['youtube_enabled'] = $section['section_feature'] === 'youtube';
        $section['map_enabled'] = $section['section_feature'] === 'map';
        $section['contact_enabled'] = $section['section_feature'] === 'contact_form';
    }
    unset($section);

    usort($data['sections'], static fn(array $a, array $b): int => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

    return $data;
}

function save_site_data(array $data): void
{
    file_put_contents(DATA_PATH, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'secao';
}

function next_section_id(array $sections): int
{
    $max = 0;
    foreach ($sections as $section) {
        $id = (int)($section['id'] ?? 0);
        if ($id > $max) {
            $max = $id;
        }
    }

    return $max + 1;
}

function sanitize_text(string $value): string
{
    return trim($value);
}

function sanitize_rich_text(string $value): string
{
    $value = trim($value);
    $value = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', '', $value) ?? '';
    $value = preg_replace('/on[a-z]+\s*=\s*"[^"]*"/i', '', $value) ?? '';
    $value = preg_replace("/on[a-z]+\s*=\s*'[^']*'/i", '', $value) ?? '';
    $value = preg_replace('/on[a-z]+\s*=\s*[^\s>]+/i', '', $value) ?? '';
    $value = preg_replace('/javascript\s*:/i', '', $value) ?? '';

    return $value;
}

function normalize_text_size(mixed $value): int
{
    $size = (int)$value;
    if ($size < 12) {
        return 12;
    }
    if ($size > 252) {
        return 252;
    }

    return $size;
}

function normalize_dimension_size(mixed $value): int
{
    $size = (int)$value;
    if ($size < 0) {
        return 0;
    }
    if ($size > 2000) {
        return 2000;
    }

    return $size;
}

function normalize_brand_display(mixed $value): string
{
    $display = trim((string)$value);
    if (!in_array($display, ['text', 'logo', 'both'], true)) {
        return 'both';
    }

    return $display;
}

function normalize_linked_images_limit(mixed $value): int
{
    $limit = (int)$value;
    if ($limit < 1) {
        return 1;
    }
    if ($limit > 30) {
        return 30;
    }

    return $limit;
}

function normalize_cards_limit(mixed $value): int
{
    $limit = (int)$value;
    if ($limit < 1) {
        return 1;
    }
    if ($limit > 8) {
        return 8;
    }

    return $limit;
}

function normalize_cards_spacing(mixed $value): string
{
    $spacing = trim((string)$value);
    if (!in_array($spacing, ['spaced', 'compact'], true)) {
        return 'spaced';
    }

    return $spacing;
}

function normalize_media_type(mixed $value): string
{
    $mediaType = trim((string)$value);
    if (!in_array($mediaType, ['image', 'video'], true)) {
        return 'image';
    }

    return $mediaType;
}

function normalize_section_function(mixed $value): string
{
    $sectionFunction = trim((string)$value);
    $allowed = [
        'basic_text',
        'split_media',
        'background_media',
        'cards_media',
        'cards_text',
        'linked_gallery',
        'youtube',
        'map',
        'contact_form',
    ];
    if (!in_array($sectionFunction, $allowed, true)) {
        return 'basic_text';
    }

    return $sectionFunction;
}

function derive_section_function_from_section(array $section): string
{
    $sectionFeature = (string)($section['section_feature'] ?? 'none');
    $sectionMode = (string)($section['section_mode'] ?? 'text');
    $layoutMode = (string)($section['layout_mode'] ?? 'background');
    $cardsStyle = (string)($section['cards_style'] ?? '');

    if ($sectionFeature === 'cards') {
        return $cardsStyle === 'text' ? 'cards_text' : 'cards_media';
    }
    if ($sectionFeature === 'linked_gallery') {
        return 'linked_gallery';
    }
    if ($sectionFeature === 'contact_form') {
        return 'contact_form';
    }
    if ($sectionFeature === 'map') {
        return 'map';
    }
    if ($sectionFeature === 'youtube') {
        return 'youtube';
    }
    if (in_array($sectionMode, ['video_background', 'video', 'video_full'], true)) {
        return 'background_media';
    }
    if ($sectionMode === 'video_split') {
        return 'split_media';
    }
    if (in_array($layoutMode, ['split-left', 'split-right'], true)) {
        return 'split_media';
    }
    if (!empty($section['background_image'])) {
        return 'background_media';
    }

    return 'basic_text';
}

function available_font_options(): array
{
    return [
        'manrope' => '"Manrope","Segoe UI",sans-serif',
        'sora' => '"Sora","Segoe UI",sans-serif',
        'georgia' => 'Georgia,"Times New Roman",serif',
        'arial' => 'Arial,"Helvetica Neue",sans-serif',
        'courier' => '"Courier New",monospace',
    ];
}

function resolve_font_family(string $fontKey): string
{
    $fonts = available_font_options();
    return $fonts[$fontKey] ?? $fonts['manrope'];
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function youtube_embed_url(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', $value, $matches) === 1) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $value) === 1) {
        return 'https://www.youtube.com/embed/' . $value;
    }

    return '';
}
