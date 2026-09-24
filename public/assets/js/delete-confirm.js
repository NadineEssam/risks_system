// حذف موحّد لكل الجداول (نفس فكرة الشكاوى):
// أي زر .delete-this فيه data-url → تأكيد SweetAlert → DELETE بـ AJAX → تحديث الجدول
$(document).on('click', '.delete-this', function () {
  const btn = $(this);
  const url = btn.data('url');
  const table = btn.closest('table');

  bootstrap.Tooltip.getInstance(this)?.hide();

  Swal.fire({
    title: 'هل أنت متأكد؟',
    text: 'لن تتمكن من التراجع عن هذا الحذف!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'نعم، احذف',
    cancelButtonText: 'إلغاء',
  }).then((result) => {
    if (! result.isConfirmed) return;

    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
      success: function (res) {
        Swal.fire({ icon: 'success', title: res.message || 'تم الحذف بنجاح', timer: 1500, showConfirmButton: false });
        if ($.fn.DataTable.isDataTable(table)) {
          table.DataTable().ajax.reload(null, false);
        }
      },
      error: function (xhr) {
        Swal.fire({
          icon: 'error',
          title: 'تعذّر الحذف',
          text: xhr.responseJSON?.message || 'حدث خطأ غير متوقع',
          confirmButtonText: 'حسناً',
        });
      },
    });
  });
});