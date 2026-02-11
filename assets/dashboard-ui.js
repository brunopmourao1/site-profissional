const sidebar = document.getElementById('adminSidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarLinks = document.querySelectorAll('.sidebar-link');
const sidebarSubLinks = document.querySelectorAll('.sidebar-sub-link');
const panels = document.querySelectorAll('.panel-card');
const breadcrumbCurrent = document.querySelector('.crumb-current');

function setBreadcrumb(link) {
  if (!breadcrumbCurrent || !link) return;
  breadcrumbCurrent.textContent = link.dataset.label || link.textContent.trim();
}

function setPanel(target) {
  panels.forEach((panel) => {
    panel.classList.toggle('is-active', panel.dataset.panel === target);
  });
}

function activateMainLink(target) {
  sidebarLinks.forEach((item) => {
    item.classList.toggle('is-active', item.dataset.target === target);
  });
}

function setSectionCollapsed(sectionItem, collapsed) {
  sectionItem.classList.toggle('is-collapsed', collapsed);
  const button = sectionItem.querySelector('.mini-toggle');
  if (button) {
    button.textContent = collapsed
      ? (button.dataset.labelClosed || 'Expandir')
      : (button.dataset.labelOpen || 'Minimizar');
  }
}

function focusSectionById(sectionId) {
  const allSections = document.querySelectorAll('.section-item[data-section-id]');
  allSections.forEach((section) => {
    const isTarget = section.dataset.sectionId === sectionId;
    setSectionCollapsed(section, !isTarget);
    if (isTarget) {
      section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
}

if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
  });
}

sidebarLinks.forEach((link) => {
  link.addEventListener('click', () => {
    const target = link.dataset.target;
    if (!target) return;

    activateMainLink(target);
    setBreadcrumb(link);
    setPanel(target);
    sidebarSubLinks.forEach((item) => item.classList.remove('is-active'));

    if (sidebar && sidebar.classList.contains('open')) {
      sidebar.classList.remove('open');
    }
  });
});

sidebarSubLinks.forEach((subLink) => {
  subLink.addEventListener('click', () => {
    const sectionId = subLink.dataset.sectionId;
    const target = subLink.dataset.target || 'secoes';
    const mainLink = document.querySelector(`.sidebar-link[data-target="${target}"]`);

    activateMainLink(target);
    setPanel(target);
    if (mainLink) setBreadcrumb(mainLink);

    sidebarSubLinks.forEach((item) => item.classList.remove('is-active'));
    subLink.classList.add('is-active');

    if (sectionId) {
      focusSectionById(sectionId);
    }

    if (sidebar && sidebar.classList.contains('open')) {
      sidebar.classList.remove('open');
    }
  });
});

const activeLink = document.querySelector('.sidebar-link.is-active');
if (activeLink) {
  setBreadcrumb(activeLink);
}

document.querySelectorAll('.collapse-toggle').forEach((button) => {
  button.addEventListener('click', () => {
    const card = button.closest('.panel-card');
    if (!card) return;
    card.classList.toggle('is-collapsed');
    button.textContent = card.classList.contains('is-collapsed')
      ? (button.dataset.labelClosed || 'Expandir')
      : (button.dataset.labelOpen || 'Minimizar');
  });
});

document.querySelectorAll('.mini-toggle').forEach((button) => {
  button.addEventListener('click', () => {
    const item = button.closest('.section-item');
    if (!item) return;
    setSectionCollapsed(item, !item.classList.contains('is-collapsed'));
  });
});

document.querySelectorAll('.section-item[data-section-id]').forEach((section) => {
  setSectionCollapsed(section, section.classList.contains('is-collapsed'));
});

const splitSizeMap = {
  small: '280px',
  medium: '420px',
  large: '560px'
};

function updateSplitSizePreview(selectElement) {
  const label = selectElement.closest('label');
  if (!label) return;
  const preview = label.querySelector('.size-preview');
  if (!preview) return;
  const formElement = selectElement.closest('form');
  const customWidthInput = formElement?.querySelector('input[name="split_width"]');
  const customWidth = customWidthInput ? Number(customWidthInput.value || 0) : 0;
  if (Number.isFinite(customWidth) && customWidth > 0) {
    preview.textContent = `Largura aplicada: ${Math.round(customWidth)}px (customizada)`;
    return;
  }
  const value = selectElement.value;
  const px = splitSizeMap[value] || splitSizeMap.medium;
  preview.textContent = `Largura estimada: ${px}`;
}

document.querySelectorAll('select[name="split_size"]').forEach((selectElement) => {
  updateSplitSizePreview(selectElement);
  selectElement.addEventListener('change', () => updateSplitSizePreview(selectElement));
  const formElement = selectElement.closest('form');
  const customWidthInput = formElement?.querySelector('input[name="split_width"]');
  if (customWidthInput) {
    customWidthInput.addEventListener('input', () => updateSplitSizePreview(selectElement));
  }
});

function parseGroupTokens(rawValue) {
  return String(rawValue || '')
    .split(/[\s,]+/)
    .map((value) => value.trim())
    .filter(Boolean);
}

function updateSectionFunctionGroups(formElement) {
  if (!(formElement instanceof HTMLFormElement)) return;
  const functionSelect = formElement.querySelector('select[name="section_function"]');
  if (!(functionSelect instanceof HTMLSelectElement)) return;

  const activeFunction = functionSelect.value || 'basic_text';
  formElement.querySelectorAll('[data-section-function]').forEach((group) => {
    const functionTokens = parseGroupTokens(group.getAttribute('data-section-function'));
    const shouldShow = functionTokens.includes(activeFunction);
    group.classList.toggle('is-hidden', !shouldShow);
  });
}

function updateSplitMediaGroups(formElement) {
  if (!(formElement instanceof HTMLFormElement)) return;
  const splitMediaSelect = formElement.querySelector('select[name="split_media_type"]');
  if (!(splitMediaSelect instanceof HTMLSelectElement)) return;

  const activeType = splitMediaSelect.value || 'image';
  formElement.querySelectorAll('[data-split-media-group]').forEach((group) => {
    const tokens = parseGroupTokens(group.getAttribute('data-split-media-group'));
    const shouldShow = tokens.includes(activeType);
    group.classList.toggle('is-hidden', !shouldShow);
  });
}

function updateBackgroundMediaGroups(formElement) {
  if (!(formElement instanceof HTMLFormElement)) return;
  const backgroundMediaSelect = formElement.querySelector('select[name="background_media_type"]');
  if (!(backgroundMediaSelect instanceof HTMLSelectElement)) return;

  const activeType = backgroundMediaSelect.value || 'image';
  formElement.querySelectorAll('[data-background-media-group]').forEach((group) => {
    const tokens = parseGroupTokens(group.getAttribute('data-background-media-group'));
    const shouldShow = tokens.includes(activeType);
    group.classList.toggle('is-hidden', !shouldShow);
  });
}

function setHeroGroupVisibility(formElement, groupName, shouldShow) {
  formElement.querySelectorAll(`[data-hero-group="${groupName}"]`).forEach((group) => {
    group.classList.toggle('is-hidden', !shouldShow);
  });
}

function updateHeroFunctionGroups(formElement) {
  if (!(formElement instanceof HTMLFormElement)) return;
  const heroFunctionSelect = formElement.querySelector('select[name="hero_function"]');
  if (!(heroFunctionSelect instanceof HTMLSelectElement)) return;

  const heroFunction = heroFunctionSelect.value || 'text';
  const heroVideoVariantSelect = formElement.querySelector('select[name="hero_video_variant"]');
  const heroImageVariantSelect = formElement.querySelector('select[name="hero_image_variant"]');
  const heroVideoVariant = heroVideoVariantSelect instanceof HTMLSelectElement ? heroVideoVariantSelect.value : 'background_text';
  const heroImageVariant = heroImageVariantSelect instanceof HTMLSelectElement ? heroImageVariantSelect.value : 'background_text';

  const isVideo = heroFunction === 'video';
  const isImage = heroFunction === 'image';
  const isTextOnly = heroFunction === 'text';
  const isVideoFull = isVideo && heroVideoVariant === 'full';
  const isImageFull = isImage && heroImageVariant === 'full';
  const showTextContent = isTextOnly || (isVideo && !isVideoFull) || (isImage && !isImageFull);
  const showSplitLayout = (isVideo && heroVideoVariant === 'split_text') || (isImage && heroImageVariant === 'split_text');
  const showMediaOnlyNote = isVideoFull || isImageFull;

  setHeroGroupVisibility(formElement, 'video_variant', isVideo);
  setHeroGroupVisibility(formElement, 'image_variant', isImage);
  setHeroGroupVisibility(formElement, 'text_content', showTextContent);
  setHeroGroupVisibility(formElement, 'split_layout', showSplitLayout);
  setHeroGroupVisibility(formElement, 'video_file', isVideo);
  setHeroGroupVisibility(formElement, 'image_file', isImage);
  setHeroGroupVisibility(formElement, 'text_only_note', isTextOnly);
  setHeroGroupVisibility(formElement, 'media_only_note', showMediaOnlyNote);
  setHeroGroupVisibility(formElement, 'extras', true);
}

document.querySelectorAll('form').forEach((formElement) => {
  const functionSelect = formElement.querySelector('select[name="section_function"]');
  if (functionSelect instanceof HTMLSelectElement) {
    updateSectionFunctionGroups(formElement);
    functionSelect.addEventListener('change', () => updateSectionFunctionGroups(formElement));
  }

  const splitMediaSelect = formElement.querySelector('select[name="split_media_type"]');
  if (splitMediaSelect instanceof HTMLSelectElement) {
    updateSplitMediaGroups(formElement);
    splitMediaSelect.addEventListener('change', () => updateSplitMediaGroups(formElement));
  }

  const backgroundMediaSelect = formElement.querySelector('select[name="background_media_type"]');
  if (backgroundMediaSelect instanceof HTMLSelectElement) {
    updateBackgroundMediaGroups(formElement);
    backgroundMediaSelect.addEventListener('change', () => updateBackgroundMediaGroups(formElement));
  }

  const heroFunctionSelect = formElement.querySelector('select[name="hero_function"]');
  if (heroFunctionSelect instanceof HTMLSelectElement) {
    updateHeroFunctionGroups(formElement);
    heroFunctionSelect.addEventListener('change', () => updateHeroFunctionGroups(formElement));
  }

  const heroVideoVariantSelect = formElement.querySelector('select[name="hero_video_variant"]');
  if (heroVideoVariantSelect instanceof HTMLSelectElement) {
    updateHeroFunctionGroups(formElement);
    heroVideoVariantSelect.addEventListener('change', () => updateHeroFunctionGroups(formElement));
  }

  const heroImageVariantSelect = formElement.querySelector('select[name="hero_image_variant"]');
  if (heroImageVariantSelect instanceof HTMLSelectElement) {
    updateHeroFunctionGroups(formElement);
    heroImageVariantSelect.addEventListener('change', () => updateHeroFunctionGroups(formElement));
  }
});
