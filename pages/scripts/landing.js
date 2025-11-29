// Landing Page: Hero Section Interactions

document.addEventListener('DOMContentLoaded', function(){
  const hero = document.getElementById('hero');
  const heroBg = hero ? hero.querySelector('.hero-bg') : null;
  const searchInputs = Array.from(document.querySelectorAll('.hero-search input, .hero-search select'));

  // Fade-in on load is handled via CSS classes; ensure repaint
  requestAnimationFrame(() => {
    document.querySelectorAll('.fade-in, .slide-up').forEach(el => {
      el.style.willChange = 'opacity, transform';
    });
  });

  // Parallax effect on scroll (throttled via rAF)
  let rafScrollPending = false;
  function onScroll(){
    if(!heroBg) return;
    if(rafScrollPending) return;
    rafScrollPending = true;
    requestAnimationFrame(() => {
      const y = window.scrollY || window.pageYOffset;
      heroBg.style.transform = `translateY(${Math.min(30, y * 0.12)}px) scale(1.02)`;
      rafScrollPending = false;
    });
  }
  window.addEventListener('scroll', onScroll, { passive: true });

  // Input focus highlight
  searchInputs.forEach(input => {
    input.addEventListener('focus', () => {
      const wrap = input.closest('.search-field');
      if(wrap){
        wrap.style.boxShadow = '0 0 0 3px rgba(255,216,77,0.25)';
      }
    });
    input.addEventListener('blur', () => {
      const wrap = input.closest('.search-field');
      if(wrap){
        wrap.style.boxShadow = 'none';
      }
    });
  });

  // Simple keyboard accessibility for Enter to submit
  const searchForm = document.querySelector('.hero-search');
  if(searchForm){
    searchForm.addEventListener('keypress', (e) => {
      if(e.key === 'Enter'){
        e.preventDefault();
        const btn = searchForm.querySelector('.btn-search');
        if(btn) btn.click();
      }
    });
    const pickup = searchForm.querySelector('#pickup');
    const ret = searchForm.querySelector('#return');
    const today = new Date();
    const pad = n => (n<10?'0':'')+n;
    const toDate = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    if(pickup){ pickup.min = toDate(today); if(!pickup.value) pickup.value = toDate(today); }
    if(ret){ ret.min = pickup && pickup.value ? pickup.value : toDate(today); }
    if(pickup && ret){
      pickup.addEventListener('change', function(){ ret.min = pickup.value; if(ret.value < pickup.value){ ret.value = pickup.value; } });
    }
  }

  // Single IntersectionObserver for reveals and stat count
  const ioReveal = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if(entry.isIntersecting){
        entry.target.classList.add('in-view');
        if(entry.target.classList.contains('stat-card')){
          entry.target.querySelectorAll('.count').forEach(animateCount);
        }
        ioReveal.unobserve(entry.target);
      }
    });
  }, { threshold: 0.05, rootMargin: '0px 0px -5% 0px' });
  document.querySelectorAll('.reveal, .stat-card').forEach(el => ioReveal.observe(el));

  function animateCount(el){
    if(el.dataset.counted) return;
    el.dataset.counted = '1';
    const target = parseInt(el.getAttribute('data-target'), 10) || 0;
    const suffix = el.getAttribute('data-suffix') || '+';
    let startTs = null;
    const dur = 1200;
    function step(ts){
      if(!startTs) startTs = ts;
      const p = Math.min(1, (ts - startTs) / dur);
      const val = Math.floor(target * p);
      el.textContent = val + suffix;
      if(p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  document.querySelectorAll('.stat-card .count').forEach(c => c.textContent = '0+');

  const tCarousel = document.getElementById('t-carousel');
  const tPrev = document.getElementById('t-prev');
  const tNext = document.getElementById('t-next');
  if(tCarousel && tPrev && tNext){
    function step(){
      const first = tCarousel.querySelector('.t-card');
      const gap = 16;
      const w = first ? first.getBoundingClientRect().width + gap : tCarousel.clientWidth * 0.8;
      return w;
    }
    tPrev.addEventListener('click', function(){ tCarousel.scrollLeft -= step(); });
    tNext.addEventListener('click', function(){ tCarousel.scrollLeft += step(); });
    let auto = setInterval(function(){ tCarousel.scrollLeft += step(); }, 4000);
    tCarousel.addEventListener('mouseenter', function(){ clearInterval(auto); });
    tCarousel.addEventListener('mouseleave', function(){ auto = setInterval(function(){ tCarousel.scrollLeft += step(); }, 4000); });
  }

  function setupFAQ(){
    var items = Array.from(document.querySelectorAll('.faq-item'));
    items.forEach(function(item){
      var btn = item.querySelector('.faq-question');
      var ans = item.querySelector('.faq-answer');
      if(!btn || !ans) return;
      ans.style.maxHeight = '0px';
      btn.addEventListener('click', function(){
        var open = item.classList.contains('open');
        items.forEach(function(it){
          if(it !== item){
            it.classList.remove('open');
            var a = it.querySelector('.faq-answer');
            var b = it.querySelector('.faq-question');
            if(a){ a.style.maxHeight = '0px'; a.hidden = true; }
            if(b){ b.setAttribute('aria-expanded','false'); }
          }
        });
        if(open){
          item.classList.remove('open');
          ans.style.maxHeight = '0px';
          ans.hidden = true;
          btn.setAttribute('aria-expanded','false');
        } else {
          item.classList.add('open');
          ans.hidden = false;
          ans.style.maxHeight = ans.scrollHeight + 'px';
          btn.setAttribute('aria-expanded','true');
        }
      });
      btn.addEventListener('keydown', function(e){
        if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); btn.click(); }
      });
    });
  }
  setupFAQ();

  function setupContact(){
    var form = document.getElementById('contactForm');
    if(!form) return;
    var success = document.getElementById('contactSuccess');
    var error = document.getElementById('contactError');
    var inputs = Array.from(form.querySelectorAll('input, textarea'));
    inputs.forEach(function(input){
      input.addEventListener('focus', function(){ var wrap = input.closest('.field'); if(wrap){ wrap.style.boxShadow = '0 0 0 3px rgba(255,216,77,0.20)'; } });
      input.addEventListener('blur', function(){ var wrap = input.closest('.field'); if(wrap){ wrap.style.boxShadow = 'none'; } });
    });
    form.addEventListener('submit', function(e){
      e.preventDefault();
      var name = form.querySelector('#fullName');
      var email = form.querySelector('#email');
      var phone = form.querySelector('#phone');
      var subject = form.querySelector('#subject');
      var message = form.querySelector('#message');
      var ok = true;
      [name,email,phone,subject,message].forEach(function(el){ if(!el || !el.value.trim()){ ok = false; } });
      var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
      if(!emailOk) ok = false;
      if(ok){
        if(error){ error.hidden = true; }
        if(success){ success.hidden = false; }
        form.reset();
      } else {
        if(success){ success.hidden = true; }
        if(error){ error.hidden = false; }
      }
    });
  }
  setupContact();

  function setupNavbar(){
    var toggle = document.getElementById('navToggle');
    var linksWrap = document.getElementById('navLinks');
    if(toggle && linksWrap){
      toggle.addEventListener('click', function(){ linksWrap.classList.toggle('open'); });
    }
    var links = Array.from(document.querySelectorAll('.nav-link'));
    links.forEach(function(link){
      if(link.hash){
        link.addEventListener('click', function(e){
          var target = document.querySelector(link.hash);
          if(target){ e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
          if(linksWrap){ linksWrap.classList.remove('open'); }
        });
      }
    });
    var sections = ['#hero','#about','#popular','#stats','#testimonials','#faq','#contact'].map(function(id){ return document.querySelector(id); }).filter(Boolean);
    var navMap = {};
    links.forEach(function(l){ if(l.hash){ navMap[l.hash] = l; } });
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          var id = '#' + entry.target.id;
          Object.values(navMap).forEach(function(a){ a.classList.remove('active'); });
          if(navMap[id]) navMap[id].classList.add('active');
        }
      });
    }, { threshold: 0.6 });
    sections.forEach(function(s){ io.observe(s); });
  }
  setupNavbar();
});