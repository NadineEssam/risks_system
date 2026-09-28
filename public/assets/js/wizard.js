/**
 * نموذج بخطوات (Form Wizard) — عام وقابل لإعادة الاستخدام في أي نموذج.
 *
 * الاستخدام في Blade:
 *   <form ... data-wizard>
 *     <div class="wizard-step" data-step-title="عنوان الخطوة الأولى"> ... </div>
 *     <div class="wizard-step" data-step-title="عنوان الخطوة الثانية"> ... </div>
 *
 *     <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
 *       <div>
 *         <button type="button" class="btn btn-outline-secondary wizard-prev d-none">السابق</button>
 *         <a href="..." class="btn btn-link text-muted">إلغاء</a>
 *       </div>
 *       <div>
 *         <button type="button" class="btn btn-primary wizard-next">التالي</button>
 *         <button type="submit" class="btn btn-success wizard-submit d-none">حفظ</button>
 *       </div>
 *     </div>
 *   </form>
 *
 * القاعدة: لا يمكن الانتقال للخطوة التالية إلا بعد استيفاء كل الحقول
 * الإلزامية (required) في الخطوة الحالية. لمجموعة اختيارات (checkbox)
 * تتطلب اختيار واحد على الأقل، أضف على العنصر الحاوي:
 *   data-require-checked-group="اسم_الحقل[]"
 * ويمكن إضافة عنصر بداخله class="wizard-group-feedback" لعرض رسالة الخطأ.
 */
(function () {
  'use strict';

  function validateStep(stepEl) {
    let valid = true;
    let firstInvalid = null;

    stepEl.querySelectorAll('input[required], select[required], textarea[required]').forEach((field) => {
      // الحقول المخفية مؤقتاً (مثل صف "مسئول آخر" الذي تمت إزالته) تُستثنى
      if (field.closest('[data-wizard-removed]')) {
        return;
      }

      if (!field.checkValidity()) {
        valid = false;
        field.classList.add('is-invalid');
        if (!firstInvalid) firstInvalid = field;
      } else {
        field.classList.remove('is-invalid');
      }
    });

    stepEl.querySelectorAll('[data-require-checked-group]').forEach((group) => {
      const name = group.getAttribute('data-require-checked-group');
      const checkedCount = group.querySelectorAll(`input[name="${CSS.escape(name)}"]:checked`).length;
      const feedback = group.querySelector('.wizard-group-feedback');

      if (checkedCount < 1) {
        valid = false;
        group.classList.add('wizard-group-invalid');
        if (feedback) feedback.style.display = 'block';
        if (!firstInvalid) firstInvalid = group;
      } else {
        group.classList.remove('wizard-group-invalid');
        if (feedback) feedback.style.display = 'none';
      }
    });

    return { valid, firstInvalid };
  }

  // responsibles.0.email → responsibles[0][email]
  function toFieldName(key) {
    const parts = key.split('.');
    return parts[0] + parts.slice(1).map((p) => `[${p}]`).join('');
  }

  /**
   * أخطاء الخادم (بعد الرجوع بـ withErrors): نعلّم كل حقل فيه خطأ ونكتب
   * الرسالة العربي تحته — حتى لو الـ view مفيهاش @error للحقل ده.
   */
  function markServerErrors() {
    const errors = window.serverErrors || {};

    Object.entries(errors).forEach(([key, messages]) => {
      const name = toFieldName(key);
      const fields = document.querySelectorAll(`[name="${CSS.escape(name)}"], [name="${CSS.escape(name + '[]')}"]`);
      if (!fields.length) return;

      const field = fields[0];
      const isChoice = field.type === 'checkbox' || field.type === 'radio';
      const message = Array.isArray(messages) ? messages[0] : messages;

      if (isChoice) {
        const group = field.closest('[data-require-checked-group]') || field.closest('.form-check')?.parentElement || field.parentElement;
        group.classList.add('wizard-group-invalid');
        if (!group.querySelector('.server-error')) {
          const div = document.createElement('div');
          div.className = 'invalid-feedback d-block server-error';
          div.textContent = message;
          group.appendChild(div);
        }
        return;
      }

      fields.forEach((f) => f.classList.add('is-invalid'));

      // لو الـ view فيها @error للحقل ده خلاص، غير كده نضيف الرسالة
      const anchor = field.closest('.input-group') || field;
      const next = anchor.nextElementSibling;
      if (!(next && next.classList.contains('invalid-feedback'))) {
        const div = document.createElement('div');
        div.className = 'invalid-feedback d-block server-error';
        div.textContent = message;
        anchor.insertAdjacentElement('afterend', div);
      }
    });
  }

  function initWizard(form) {
    const steps = Array.from(form.querySelectorAll(':scope > .wizard-step'));
    if (steps.length < 2) return;

    const prevBtn = form.querySelector('.wizard-prev');
    const nextBtn = form.querySelector('.wizard-next');
    const submitBtn = form.querySelector('.wizard-submit');

    // بناء شريط الخطوات تلقائياً
    const nav = document.createElement('div');
    nav.className = 'wizard-nav';
    steps.forEach((step, i) => {
      const title = step.getAttribute('data-step-title') || `الخطوة ${i + 1}`;
      const item = document.createElement('div');
      item.className = 'wizard-nav-item';
      item.innerHTML = `<span class="wizard-nav-circle">${i + 1}</span><span class="wizard-nav-label">${title}</span>`;
      nav.appendChild(item);
    });
    form.insertBefore(nav, steps[0]);
    const navItems = Array.from(nav.querySelectorAll('.wizard-nav-item'));

    // إذا رجع النموذج بأخطاء تحقق من الخادم (بعد إعادة توجيه)، ابدأ من أول
    // خطوة بها خطأ بدلاً من الخطوة الأولى دائماً.
    let current = 0;
    const stepWithServerError = steps.findIndex((step) => step.querySelector('.is-invalid, .wizard-group-invalid'));
    if (stepWithServerError !== -1) {
      current = stepWithServerError;
    }

    function render() {
      steps.forEach((step, i) => {
        step.style.display = i === current ? '' : 'none';
      });

      navItems.forEach((item, i) => {
        item.classList.toggle('is-active', i === current);
        item.classList.toggle('is-completed', i < current);
      });

      if (prevBtn) prevBtn.classList.toggle('d-none', current === 0);

      const isLastStep = current === steps.length - 1;
      if (nextBtn) nextBtn.classList.toggle('d-none', isLastStep);
      if (submitBtn) submitBtn.classList.toggle('d-none', !isLastStep);

      const card = form.closest('.card-body') || form;
      card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function focusInvalid(firstInvalid) {
      if (!firstInvalid) return;
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (typeof firstInvalid.focus === 'function') firstInvalid.focus();
      if (typeof firstInvalid.reportValidity === 'function') firstInvalid.reportValidity();
    }

    function goNext() {
      const { valid, firstInvalid } = validateStep(steps[current]);
      if (!valid) {
        focusInvalid(firstInvalid);
        return;
      }
      if (current < steps.length - 1) {
        current++;
        render();
      }
    }

    function goPrev() {
      if (current > 0) {
        current--;
        render();
      }
    }

    if (nextBtn) nextBtn.addEventListener('click', goNext);
    if (prevBtn) prevBtn.addEventListener('click', goPrev);

    // الضغط على رقم الخطوة: الرجوع مسموح دايماً — التقدم لازم الخطوات
    // اللي قبلها تكون مكتملة (لو فيه خطوة ناقصة نقف عندها ونوضح الخطأ)
    function goTo(target) {
      if (target === current) return;

      if (target < current) {
        current = target;
        render();
        return;
      }

      for (let s = current; s < target; s++) {
        const { valid, firstInvalid } = validateStep(steps[s]);
        if (!valid) {
          current = s;
          render();
          focusInvalid(firstInvalid);
          return;
        }
      }

      current = target;
      render();
    }

    navItems.forEach((item, i) => {
      item.style.cursor = 'pointer';
      item.setAttribute('title', 'الانتقال إلى هذه الخطوة');
      item.addEventListener('click', () => goTo(i));
    });

    // منع الانتقال المباشر للحفظ بمفتاح Enter من أي خطوة قبل الأخيرة —
    // بدلاً من ذلك تُعامل كضغطة "التالي".
    form.addEventListener('keydown', (e) => {
      if (e.key !== 'Enter') return;
      if (e.target.tagName === 'TEXTAREA') return;
      if (current !== steps.length - 1) {
        e.preventDefault();
        goNext();
      }
    });

    // حارس أخير عند الإرسال الفعلي: يتحقق من كل الخطوات مجدداً (وليس فقط
    // الخطوة الحالية) قبل السماح بإرسال النموذج فعلياً للخادم.
    form.addEventListener('submit', (e) => {
      let firstInvalidStep = -1;
      let firstInvalidField = null;

      steps.forEach((step, i) => {
        const { valid, firstInvalid } = validateStep(step);
        if (!valid && firstInvalidStep === -1) {
          firstInvalidStep = i;
          firstInvalidField = firstInvalid;
        }
      });

      if (firstInvalidStep !== -1) {
        e.preventDefault();
        current = firstInvalidStep;
        render();
        focusInvalid(firstInvalidField);
      }
    });

    render();
  }

  document.addEventListener('DOMContentLoaded', () => {
    markServerErrors();
    document.querySelectorAll('form[data-wizard]').forEach(initWizard);
  });
})();
