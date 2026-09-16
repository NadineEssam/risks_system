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
    document.querySelectorAll('form[data-wizard]').forEach(initWizard);
  });
})();
