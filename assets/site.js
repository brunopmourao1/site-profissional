const menuToggle = document.querySelector('.menu-toggle');
const menu = document.getElementById('menu');

if (menuToggle && menu) {
  menuToggle.addEventListener('click', () => {
    menu.classList.toggle('open');
  });

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => menu.classList.remove('open'));
  });
}

const revealElements = document.querySelectorAll('.reveal');

function revealNow(element) {
  element.classList.add('is-visible');
}

function prepareLetterAnimation(container) {
  if (!container.classList.contains('anim-letters')) return;

  container.querySelectorAll('h1, h2, p').forEach((node) => {
    if (node.dataset.lettersReady === '1') return;
    if (node.children.length > 0) return;

    const text = node.textContent || '';
    if (!text.trim()) return;

    const fragment = document.createDocumentFragment();
    let index = 0;
    [...text].forEach((char) => {
      const span = document.createElement('span');
      span.className = 'letter-char';
      span.style.setProperty('--i', String(index));
      span.textContent = char === ' ' ? '\u00A0' : char;
      fragment.appendChild(span);
      index += 1;
    });

    node.textContent = '';
    node.appendChild(fragment);
    node.dataset.lettersReady = '1';
  });
}

revealElements.forEach((element, index) => {
  prepareLetterAnimation(element);
  if (!element.classList.contains('anim-none')) {
    element.style.transitionDelay = `${Math.min(index * 70, 320)}ms`;
  }
});

function shouldReveal(element) {
  const rect = element.getBoundingClientRect();
  const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
  const enterPoint = viewportHeight * 0.86;
  const leavePoint = 0;
  return rect.top <= enterPoint && rect.bottom >= leavePoint;
}

function updateReveal() {
  revealElements.forEach((element) => {
    if (element.classList.contains('anim-none')) {
      revealNow(element);
      return;
    }

    if (shouldReveal(element)) {
      revealNow(element);
    } else {
      element.classList.remove('is-visible');
    }
  });
}

function replayRevealInContainer(container) {
  if (!container) return;

  const innerReveal = container.querySelectorAll('.reveal');
  innerReveal.forEach((element) => {
    if (element.classList.contains('anim-none')) return;
    element.classList.remove('is-visible');
  });

  requestAnimationFrame(() => {
    innerReveal.forEach((element) => {
      if (element.classList.contains('anim-none')) {
        revealNow(element);
        return;
      }
      if (shouldReveal(element)) {
        revealNow(element);
      }
    });
  });
}

let ticking = false;
function onScrollOrResize() {
  if (ticking) return;
  ticking = true;
  requestAnimationFrame(() => {
    updateReveal();
    ticking = false;
  });
}

window.addEventListener('scroll', onScrollOrResize, { passive: true });
window.addEventListener('resize', onScrollOrResize);
window.addEventListener('load', updateReveal);
window.addEventListener('hashchange', () => {
  const targetId = window.location.hash ? window.location.hash.slice(1) : '';
  const targetElement = targetId ? document.getElementById(targetId) : null;
  if (!targetElement) {
    updateReveal();
    return;
  }
  replayRevealInContainer(targetElement);
});

document.querySelectorAll('.menu a[href^="#"]').forEach((link) => {
  link.addEventListener('click', () => {
    const targetId = link.getAttribute('href')?.slice(1) || '';
    const targetElement = targetId ? document.getElementById(targetId) : null;
    if (!targetElement) return;
    setTimeout(() => replayRevealInContainer(targetElement), 120);
  });
});
updateReveal();

document.querySelectorAll('.js-mailto-form').forEach((formElement) => {
  if (!(formElement instanceof HTMLFormElement)) return;

  formElement.addEventListener('submit', (event) => {
    if (!formElement.reportValidity()) return;

    event.preventDefault();

    const destination = (formElement.dataset.destination || '').trim();
    if (!destination) return;

    const sectionTitle = (formElement.dataset.sectionTitle || 'Contato').trim();
    const name = (formElement.querySelector('[name="name"]')?.value || '').trim();
    const phone = (formElement.querySelector('[name="phone"]')?.value || '').trim();
    const email = (formElement.querySelector('[name="email"]')?.value || '').trim();
    const message = (formElement.querySelector('[name="message"]')?.value || '').trim();

    const subject = `[Site] ${sectionTitle} - ${name}`;
    const body = `Nome: ${name}\nTelefone: ${phone}\nE-mail: ${email}\n\nMensagem:\n${message}`;
    const mailtoUrl = `mailto:${destination}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

    window.location.href = mailtoUrl;
  });
});

function initCarousel(carouselElement) {
  if (!(carouselElement instanceof HTMLElement)) return;

  const viewport = carouselElement.querySelector('.carousel-viewport');
  const track = carouselElement.querySelector('.carousel-track');
  if (!(viewport instanceof HTMLElement)) return;
  if (!(track instanceof HTMLElement)) return;

  const slides = Array.from(track.querySelectorAll('.carousel-slide'));
  if (slides.length < 2) return;

  const prevButton = carouselElement.querySelector('.carousel-prev');
  const nextButton = carouselElement.querySelector('.carousel-next');
  const dots = Array.from(carouselElement.querySelectorAll('.carousel-dot'));

  let activeIndex = 0;

  function clampIndex(index) {
    const total = slides.length;
    if (total <= 0) return 0;
    const normalized = ((index % total) + total) % total;
    return normalized;
  }

  function syncVideos() {
    slides.forEach((slide, index) => {
      const isActive = index === activeIndex;
      slide.classList.toggle('is-active', isActive);
      slide.querySelectorAll('video').forEach((videoElement) => {
        if (!(videoElement instanceof HTMLVideoElement)) return;
        if (isActive) {
          const promise = videoElement.play();
          if (promise && typeof promise.catch === 'function') {
            promise.catch(() => {});
          }
        } else {
          videoElement.pause();
        }
      });
    });
  }

  function render() {
    track.style.transform = `translateX(-${activeIndex * 100}%)`;
    dots.forEach((dot) => {
      const dotIndex = Number(dot.dataset.slideIndex || 0);
      dot.classList.toggle('is-active', dotIndex === activeIndex);
      dot.setAttribute('aria-selected', String(dotIndex === activeIndex));
    });
    syncVideos();
  }

  function goTo(index) {
    activeIndex = clampIndex(index);
    render();
  }

  if (prevButton instanceof HTMLElement) {
    prevButton.addEventListener('click', () => goTo(activeIndex - 1));
  }
  if (nextButton instanceof HTMLElement) {
    nextButton.addEventListener('click', () => goTo(activeIndex + 1));
  }
  dots.forEach((dot) => {
    dot.addEventListener('click', () => {
      const dotIndex = Number(dot.dataset.slideIndex || 0);
      goTo(dotIndex);
    });
  });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      slides.forEach((slide) => {
        slide.querySelectorAll('video').forEach((videoElement) => {
          if (videoElement instanceof HTMLVideoElement) {
            videoElement.pause();
          }
        });
      });
      return;
    }
    syncVideos();
  });

  render();
}

document.querySelectorAll('.carousel[data-carousel="true"]').forEach(initCarousel);
